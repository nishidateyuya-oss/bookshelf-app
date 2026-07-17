<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('書籍一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8 w-full bg-white shadow-sm rounded-lg p-6">
                <form action="{{ route('books.index') }}" method="GET" class="space-y-4">

                    <div class=" space-y-4">

                        <div class="flex items-center space-x-4 text-sm font-bold text-gray-700">
                            <div class="w-2/5">キーワード</div>
                            <div class="w-2/5">ジャンル</div>
                            <div class="w-2/5">並び順</div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="w-2/5">
                                <input type="text" name="keyword" value="{{ request('keyword') }}"
                                    placeholder="タイトル・著者で検索..."
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="w-2/5">
                                <select name="genre_id"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">すべて</option>
                                    @foreach ($genres as $genre)
                                        <option value="{{ $genre->id }}"
                                            {{ request('genre_id') == $genre->id ? 'selected' : '' }}>
                                            {{ $genre->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="w-2/5">
                                <select name="sort"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>登録日が新しい順
                                    </option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>登録日が古い順
                                    </option>
                                    <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>タイトル順
                                    </option>
                                    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>評価が高い順
                                    </option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="flex items-center justify-between pt-2">
                        <div class="flex items-center space-x-2">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded text-sm transition duration-150">
                                検索
                            </button>
                            <a href="{{ route('books.index') }}"
                                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded text-sm transition duration-150">
                                リセット
                            </a>
                        </div>

                        <div>
                            <a href="{{ route('books.create') }}"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm inline-block">
                                書籍を登録
                            </a>
                        </div>
                    </div>

                </form>
            </div>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($books->isEmpty())
                        <p class="text-gray-500">書籍が登録されていません。</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($books as $book)
                                <a href="{{ route('books.show', $book) }}"
                                    class="block border rounded-lg p-4 shadow hover:shadow-lg transition cursor-pointer">
                                    @if ($book->image_url)
                                        <img src="{{ $book->image_url }}" alt="{{ $book->title }}"
                                            class="w-full h-48 object-cover mb-4 rounded">
                                    @else
                                        <div
                                            class="w-full h-48 bg-gray-200 flex items-center justify-center mb-4 rounded">
                                            <span class="text-gray-500">画像なし</span>
                                        </div>
                                    @endif
                                    <h3 class="font-bold text-lg mb-2 text-blue-600 hover:text-blue-800">
                                        {{ $book->title }}
                                    </h3>
                                    <p class="text-gray-600 text-sm mb-2">{{ $book->author }}</p>
                                    <div class="flex flex-wrap gap-1 mb-2">
                                        @foreach ($book->genres as $genre)
                                            <span
                                                class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded">{{ $genre->name }}</span>
                                        @endforeach
                                    </div>
                                    @if ($book->reviews_avg_rating)
                                        <div class="flex items-center">
                                            <span class="text-yellow-500">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= round($book->reviews_avg_rating))
                                                        ★
                                                    @else
                                                        ☆
                                                    @endif
                                                @endfor
                                            </span>
                                            <span class="text-sm text-gray-500 ml-2">
                                                ({{ number_format($book->reviews_avg_rating, 1) }})
                                            </span>
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $books->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
