<div>
    <!-- Knowing is not enough; we must apply. Being willing is not enough; we must do. - Leonardo da Vinci -->
</div>

<!-- resources/views/tweets/index.blade.php -->

<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('アップロード一覧') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
          @foreach ($uploads as $upload)
          <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
            <p class="text-gray-800 dark:text-gray-300">{{ $upload->title }}</p>
            <p class="text-gray-600 dark:text-gray-400 text-sm">投稿者: {{ $upload->user->name }}</p>
            <a href="{{ route('uploads.show', $upload) }}" class="text-blue-500 hover:text-blue-700">詳細を見る</a>
          
            <a href="{{ route('extractions.create', ['upload_id' => $upload->id]) }}" class="ml-4 text-blue-500 hover:text-blue-700">抽出</a>

          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>



</x-app-layout>

