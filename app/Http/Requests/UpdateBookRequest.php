<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $book = $this->route('book');

        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'size:13', Rule::unique('books', 'isbn')->ignore($book->id)],
            'published_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url', 'max:255'],
            'genres' => ['required', 'array', 'min:1'],
            'genres.*' => ['integer', 'exists:genres,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => '本のタイトルを入力してください',
            'title.max' => 'タイトルは255文字以内で入力してください',
            'author.required' => '著者を入力してください',
            'author.max' => '著者名は255文字以内で入力してください',
            'isbn.required' => 'ISBNを入力してください',
            'isbn.size' => 'ISBNは13桁で入力してください',
            'isbn.unique' => 'そのISBNは既に使われています',
            'published_date.required' => '出版日を入力してください',
            'published_date.date' => '出版日は有効な日付で入力してください',
            'image_url.max' => '画像URLは255文字以内で入力してください',
            'image_url.url' => '有効な画像URLを入力してください',
            'genres.required' => 'ジャンルを1つ以上選択してください',
            'genres.*.exists' => '選択されたジャンルは存在しません',
        ];
    }
}
