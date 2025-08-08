<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Product;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
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
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Scope: Only active and approved users by default
    protected static function booted()
    {
        // DISABLED: Do not restrict by is_active/is_approved for login or queries
        // static::addGlobalScope('activeApproved', function ($query) {
        //     if (!request()->has('all')) {
        //         $query->where('is_active', 1)->where('is_approved', 1);
        //     }
        // });
    }

    // Optional: Local scope for clarity
    public function scopeActiveApproved($query)
    {
        return $query->where('is_active', 1)->where('is_approved', 1);
    }

    // Static method to fetch users, with option to bypass global scope
    public static function getUsers($mode = null)
    {
        if ($mode === 'all') {
            return static::withoutGlobalScope('activeApproved')->get();
        }
        return static::all(); // applies global scope
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_user');
    }
}
