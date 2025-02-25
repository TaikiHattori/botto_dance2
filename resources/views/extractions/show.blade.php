<div>
    <!-- The biggest battle is the war against ignorance. - Mustafa Kemal Atatürk -->
</div>

<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('プレイリスト詳細') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
          <a href="{{ route('extractions.index') }}" class="text-blue-500 hover:text-blue-700 mr-2">一覧に戻る</a>
          <p class="text-black dark:text-gray-300 text-lg">開始：{{ substr($extraction->start, 3) }}</p>
          <p class="text-black dark:text-gray-300 text-lg">終了：{{ substr($extraction->end, 3) }}</p>

          <div class="text-black dark:text-gray-400 text-sm">
            <!-- <p>作成日時: {{ $extraction->created_at->format('Y-m-d H:i') }}</p>
            <p>更新日時: {{ $extraction->updated_at->format('Y-m-d H:i') }}</p> -->
          </div>

          @if (auth()->id() == $extraction->upload->user_id)
            <div class="flex mt-4">
            <a href="{{ route('extractions.edit', $extraction) }}" class="text-blue-500 hover:text-blue-700 mr-2">編集</a>

            <form action="{{ route('extractions.destroy', $extraction) }}" method="POST" onsubmit="return confirm('本当に削除しますか？');">
              @csrf
              @method('DELETE')
              <button type="submit" class="text-red-500 hover:text-red-700">削除</button>
            </form>
            </div>
          @endif

        </div>
      </div>
    </div>
  </div>
</x-app-layout>