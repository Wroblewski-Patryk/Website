<x-filament-panels::page
    @class([
        'fi-resource-edit-record-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'fi-resource-record-' . (isset($record) ? $record->getKey() : 'new'),
    ])
>
    <!-- Custom Layout Wrapper -->
    <div class="flex flex-col lg:flex-row gap-6 items-start h-[calc(100vh-10rem)] w-full max-w-full">
        
        <!-- Center: Iframe Preview -->
        <div class="w-full lg:flex-1 h-full bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-white/10 overflow-hidden relative">
            <iframe 
                id="live-preview-iframe"
                src="{{ route('live-preview') }}" 
                class="w-full h-full border-none"
            ></iframe>
        </div>

        <!-- Right: Form panel -->
        <div class="w-full lg:w-[450px] shrink-0 h-full overflow-y-auto pr-2 custom-scrollbar">
            <x-filament-panels::form
                id="form"
                :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
                wire:submit="save"
            >
                {{ $this->form }}

                <div class="mt-6">
                    <x-filament-panels::form.actions
                        :actions="method_exists($this, 'getCachedFormActions') ? $this->getCachedFormActions() : []"
                        :full-width="method_exists($this, 'hasFullWidthFormActions') ? $this->hasFullWidthFormActions() : true"
                    />
                </div>
            </x-filament-panels::form>
            
            <!-- Render relation managers if they exist -->
            @php
                $relationManagers = method_exists($this, 'getRelationManagers') ? $this->getRelationManagers() : [];
            @endphp
            @if (count($relationManagers))
                <div class="mt-8">
                    <x-filament-panels::resources.relation-managers
                        :active-locale="isset($activeLocale) ? $activeLocale : null"
                        :active-manager="$this->activeRelationManager ?? null"
                        :managers="$relationManagers"
                        :owner-record="$record ?? null"
                        :page-class="static::class"
                    />
                </div>
            @endif
        </div>
    </div>

    <!-- Alpine.js to sync Livewire data into the iframe -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('visualBuilder', () => ({
                init() {
                    // Initial sync when iframe says it's ready
                    window.addEventListener('message', (e) => {
                        if(e.data && e.data.type === 'preview-ready') {
                            this.syncToIframe();
                            
                            // Send initial payload immediately because Livewire state is already mounted
                            setTimeout(() => this.syncToIframe(), 500); 
                        }
                    });

                    // Hook into Livewire lifecycle to sync changes
                    Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                        succeed(({ snapshot, effect }) => {
                            setTimeout(() => this.syncToIframe(), 50);
                        })
                    })
                },
                syncToIframe() {
                    const iframe = document.getElementById('live-preview-iframe');
                    if(iframe && iframe.contentWindow) {
                        try {
                            // Fetch current Livewire form state directly via Alpine/Livewire proxy
                            const data = @this.data;
                            if (data) {
                                iframe.contentWindow.postMessage({
                                    type: 'filament-block-update',
                                    payload: {
                                        title: data.title,
                                        content: data.content,
                                        header_override_id: data.header_override_id,
                                        footer_override_id: data.footer_override_id
                                    }
                                }, '*');
                            }
                        } catch (e) {
                            console.error('Builder Sync Error:', e);
                        }
                    }
                }
            }));
        });
    </script>
    <div x-data="visualBuilder"></div>

    <style>
    /* Full width override for the Filament page container */
    .fi-main {
        max-width: 100% !important;
        padding-left: 1.5rem !important;
        padding-right: 1.5rem !important;
    }
    .fi-header {
        max-width: 100% !important;
    }
    
    /* Optional styling for the scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: rgba(156, 163, 175, 0.5);
        border-radius: 20px;
    }
    </style>
</x-filament-panels::page>
