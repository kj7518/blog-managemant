<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Storage;

class Blog extends Model
{
    protected $table = 'blogs';
    
    protected $fillable = [
        'title','description','start_date','end_date','is_active','image','user_id'
    ];

    protected $appends = ['is_my_blog'];

    public function getImageAttribute($image)
    {       
         return $image ? url('storage/albums/'.$image): $image;             
    }

    public function getIsMyBlogAttribute()
    {       
        $user_id = $this->attributes['user_id'];
        $user = Auth::user();

        return $this->attributes['is_my_blog'] = $user && $user->id == $user_id ? 1 : 0;             
    }
}
