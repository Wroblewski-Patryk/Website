<template>
  <div class="relative min-h-screen">
    <!-- Legacy background structure -->
    <div id="color" class="fixed inset-0 z-[-3]"></div>
    <div id="image" class="fixed inset-0 z-[-2]">
        <div id="image-1" class="absolute inset-0"></div>
        <div id="image-2" class="absolute inset-0"></div>
        <div id="image-3" class="absolute inset-0"></div>
    </div>
    <div id="border" class="fixed inset-0 pointer-events-none z-50 transition-all duration-300"></div>

    <!-- Header Wrapper -->
    <header v-if="$page.props.header">
      <DynamicBlock 
        v-for="(block, index) in $page.props.header" 
        :key="'header-'+index" 
        :block="block" 
      />
    </header>

    <!-- Main Content -->
    <main>
      <slot />
    </main>

    <!-- Footer Wrapper -->
    <footer v-if="$page.props.footer">
      <DynamicBlock 
        v-for="(block, index) in $page.props.footer" 
        :key="'footer-'+index" 
        :block="block" 
      />
    </footer>
  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import DynamicBlock from '@/Components/DynamicBlock.vue'

// Basic styles for the legacy background structure
</script>

<style>
/* Base port of !oldCode animated background styles */
#color {
  background-color: #262626; /* Default dark */
}
#border {
  border: 4px solid rgba(255,255,255,0.1);
  margin: 10px;
  /* Adjust based on screen size, matching legacy */
}

@media (min-width: 768px) {
  #border {
    border-width: 8px;
    margin: 20px;
  }
}
</style>
