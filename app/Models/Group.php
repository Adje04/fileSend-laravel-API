<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'user_id',
    ];




    public function user()
    {
        return $this->belongsTo(User::class);
    }


    // Relation avec les membres 
    public function members()
    {
        return $this->belongsToMany(User::class, 'members', 'group_id', 'user_id')
            ->withTimestamps();
    }


    public function files()
    {
        return $this->hasMany(File::class);
    }
}
