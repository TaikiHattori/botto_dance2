{{-- <div>
    <!-- Simplicity is the consequence of refined emotions. - Jean D'Alembert -->
</div> --}}


<!-- resources/views/tweets/create.blade.php -->

<x-app-layout>
  <x-slot name="header">
    <!-- <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('アップロード作成') }}
    </h2> -->
  </x-slot>

  <style>
    html, body {
      color: #ffffff; /* テキスト色を白に */
      margin: 0;
      padding: 0;
    }


    .text-gray-800, .text-gray-900, .dark\:text-gray-100 {
      color: #ffffff !important; /* テキスト色を白に */
    }
    .bg-blue-500 {
      background-color: #333333 !important; /* ボタンの背景色を黒っぽく */
    }

    .bg-gray-100 {
      background-color: #1f1f1f !important; /* 背景色を黒っぽく */
    }
  </style>

  {{-- <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('uploads.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="file" class="block text-sm font-medium text-white-700 dark:text-gray-300">mp3ファイルを選択</label>
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
    </div> --}}


<div class="px-4 flex justify-center items-center" style="min-height: 50vh;">
  <form action="{{ route('uploads.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <label for="dropzone-file" class="flex flex-col items-center w-full max-w-lg p-12 mx-auto mt-2 text-center border-2 border-gray-300 border-dashed cursor-pointer dark:bg-gray-900 dark:border-gray-700 rounded-xl" style="box-shadow: 0px 0px 30px 10px rgb(255 255 255 / 80%);">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-white">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
        </svg>

        <h2 class="mt-1 tracking-wide text-white dark:text-gray-200">Music files</h2>

        <p class="mt-2 tracking-wide text-white dark:text-gray-400">Upload your file mp3. </p>

        @error('upload')
        <span class="text-red-500 text-xs italic">{{ $message }}</span>
        @enderror

        <input id="dropzone-file" type="file" name="file" class="hidden" required onchange="updateFileName(this)" />
        <span id="file-name" class="text-white"></span> <!-- アップロードファイル名を表示するための要素 -->

    </label>
    <button type="submit" class="w-full border-solid border border-white mt-4 hover:opacity-80 text-white font-bold py-2 px-4 rounded">アップロード</button>

  </form>
</div>


</x-app-layout>

<script>
    function updateFileName(input) {
        const fileName = input.files[0] ? input.files[0].name : 'ファイルを選択してください';
        document.getElementById('file-name').textContent = fileName; // ファイル名を表示
    }
</script>
