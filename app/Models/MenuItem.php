<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class MenuItem extends Model
{
    use HasFactory, NodeTrait;

    protected $table ='menu_items';

    protected $guarded = [];

    public function reference()
    {
        return $this->morphTo();
    }

    public function titleReferenceType(){
        return match($this->reference_type){
            Category::class => trans('Danh mục'),
            Category::class => trans('Chuyên mục'),
            Post::class => trans('Bài viết'),
            Page::class => trans('Trang'),
            default => trans('Link')
        };
    }

    public function getUrl(){
        return match($this->reference_type){
            Post::class => route('post.show',  $this->reference->slug),
            Page::class => route('page.show', $this->reference->slug),
            default => url($this->url)
        };
    }

    public function hasChild(){
        return $this->children->count() > 0;
    }
}
