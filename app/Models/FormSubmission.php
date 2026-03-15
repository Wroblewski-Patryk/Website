<?php
  
  namespace App\Models;
  
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Factories\HasFactory;
  
  class FormSubmission extends Model
  {
      use HasFactory;
  
      protected $fillable = [
          'form_id',
          'data',
          'ip_address',
          'user_agent'
      ];
  
      protected $casts = [
          'data' => 'array',
      ];
  
      public function form()
      {
          return $this->belongsTo(Form::class);
      }
  }
  
