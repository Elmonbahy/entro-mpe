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

  // Di dalam class User
  public function getIsOnlineAttribute(): bool
  {
    // Jika di controller Anda sudah melakukan join 'last_interaction', gunakan itu
    if (isset($this->last_interaction)) {
      return $this->last_interaction >= now()->subMinutes(5)->getTimestamp();
    }

    // Jika tidak ada (fallback), baru lakukan query manual
    return \DB::table('sessions')
      ->where('user_id', $this->id)
      ->where('last_activity', '>=', now()->subMinutes(5)->getTimestamp())
      ->exists();
  }

  public function role()
  {
    return $this->belongsTo(Role::class);
  }


  public function hasRole($role)
  {
    return $this->role && $this->role->slug === $role;
  }

  /**
   * Check if the user has any of the given roles.
   *
   * @param array|string $roles An array of role slugs or a single role slug.
   * @return bool
   */
  public function hasAnyRole($roles)
  {
    // Checks if the user's role slug is in the provided array of roles.
    return $this->role && in_array($this->role->slug, (array) $roles);
  }

  public function getRoutePrefix(): string
  {
    // Return a default prefix if the user has no role.
    if (!$this->role) {
      return 'dashboard';
    }

    return match ($this->role->slug) {
      'af' => 'fakturis',
      'ag' => 'gudang',
      'aa' => 'accounting',
      'aw' => 'warehouse',
      'as' => 'supervisor',
      'su' => 'superadmin',
      default => 'dashboard',
    };
  }

}
