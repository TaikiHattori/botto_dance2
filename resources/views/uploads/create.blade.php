<div>
    <!-- Simplicity is the consequence of refined emotions. - Jean D'Alembert -->
</div>


<!-- resources/views/tweets/create.blade.php -->

<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('アップロード作成') }}
    </h2>
  </x-slot>

  

  <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('uploads.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">MP3ファイルを選択</label>
                            <input type="file" name="file" id="file" class="mt-1 block w-full" required>
                        </div>
                        
                        @error('upload')
                        <span class="text-red-500 text-xs italic">{{ $message }}</span>
                        @enderror
                        <div class="mb-4">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                アップロード
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>