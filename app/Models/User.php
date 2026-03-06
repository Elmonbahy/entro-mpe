<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
  /** @use HasFactory<\Database\Factories\UserFactory> */
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'username',
    'email',
    'password',
    'role_id',
    'login_attempts',
    'locked_until'
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Get the attributes that should be cast.
   *
   */
  protected $casts = [
    'locked_until' => 'datetime',
    'login_attempts' => 'integer',
    'role_id' => 'integer',
    'password' => 'hashed',
  ];

  public function role()
  {
    return $this->belongsTo(Role::class);
  }


  public function hasRole($role)
  {
    return $this->role->slug === $role;
  }

  public function hasAnyRole($roles)
  {
    return in_array($this->role->slug, (array) $roles);
  }

  public function getRoutePrefix(): string
  {
    return match ($this->role->slug) {
      'af' => 'fakturis',
      'ag' => 'gudang',
      'aa' => 'accounting',
      'aw' => 'warehouse',
      'as' => 'supervisor',
      default => 'dashboard',
    };
  }

}
