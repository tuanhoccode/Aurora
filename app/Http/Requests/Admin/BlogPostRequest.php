<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogPostRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('blog_posts', 'slug')->ignore($this->route('post'))
            ],
            'content' => 'required|string',
            'category_id' => 'required|exists:blog_categories,id',
            'is_active' => 'sometimes|boolean',
            'allow_comments' => 'sometimes|boolean',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        if ($this->isMethod('post')) {
            $rules['thumbnail'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'title.required' => 'Vui lòng nhập tiêu đề bài viết',
            'slug.required' => 'Vui lòng nhập đường dẫn SEO',
            'slug.unique' => 'Đường dẫn SEO đã tồn tại',
            'content.required' => 'Vui lòng nhập nội dung bài viết',
            'category_id.required' => 'Vui lòng chọn danh mục',
            'category_id.exists' => 'Danh mục không tồn tại',
            'is_active.boolean' => 'Trạng thái không hợp lệ',
            'allow_comments.boolean' => 'Trạng thái cho phép bình luận không hợp lệ',
            'thumbnail.required' => 'Vui lòng chọn ảnh đại diện',
            'thumbnail.image' => 'File phải là ảnh',
            'thumbnail.mimes' => 'Định dạng ảnh không hợp lệ (jpeg, png, jpg, gif)',
            'thumbnail.max' => 'Kích thước ảnh tối đa là 2MB',
        ];
    }
}
