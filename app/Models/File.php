<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

     protected $fillable = ['name', 'path', 'size', 'type',  'user_id', 'group_id'];
    protected $guarded = [];


    public function user(){

        return $this->belongsTo(User::class);
    }

    public function group(){

        return $this->belongsTo(Group::class);
    }


    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    // public function getSizeAttribute($value){
    //     return round($value / 1024, 2).'KB';
    // }

    // public function getDownloadLinkAttribute(){
    //     return url('storage/'.$this->path);
    // }

    
}
