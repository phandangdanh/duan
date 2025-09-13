<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DanhMuc extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'danhmuc';
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'parent_id',
        'sort_order',
        'status'
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'sort_order' => 'integer',
    ];

    // Relationship với danh mục cha
    public function parent()
    {
        return $this->belongsTo(DanhMuc::class, 'parent_id');
    }

    // Relationship với danh mục con
    public function children()
    {
        return $this->hasMany(DanhMuc::class, 'parent_id');
    }

    // Lấy tất cả danh mục con (đệ quy)
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    // Lấy tất cả danh mục cha (đệ quy)
    public function allParents()
    {
        return $this->parent()->with('allParents');
    }

    // Scope lấy danh mục active
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope lấy danh mục gốc (parent_id = 0)
    public function scopeRoot($query)
    {
        return $query->where('parent_id', 0);
    }

    // Tự động tạo slug khi set name
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    // Tự động tạo slug khi set slug
    public function setSlugAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['slug'] = Str::slug($this->name ?? 'category');
        } else {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    // Boot method để xử lý các event
    protected static function boot()
    {
        parent::boot();

        // Trước khi tạo, đảm bảo có slug
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
            
            // Đảm bảo parent_id là số
            if (empty($category->parent_id) || $category->parent_id == 0 || $category->parent_id == '') {
                $category->parent_id = 0;
            } else {
                $category->parent_id = (int) $category->parent_id;
            }
            
            // Đảm bảo sort_order là số
            if (empty($category->sort_order)) {
                $category->sort_order = 0;
            }
            
            // Đảm bảo status có giá trị
            if (empty($category->status)) {
                $category->status = 'active';
            }
        });

        // Trước khi update, đảm bảo có slug
        static::updating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // Accessor để lấy URL ảnh
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('backend/img/default-category.png');
    }

    // Kiểm tra có danh mục con không
    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    // Lấy cấp độ của danh mục
    public function getLevelAttribute()
    {
        $level = 0;
        $parent = $this->parent;
        while ($parent) {
            $level++;
            $parent = $parent->parent;
        }
        return $level;
    }

    
}
