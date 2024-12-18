<div>
    <!-- The whole future lies in uncertainty: live immediately. - Seneca -->
</div>

<x-app-layout>
  <x-slot name="header">
    <!-- <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('抽出一覧') }}
    </h2> -->
  </x-slot>

  <style>
  </style>

  {{-- <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
          @foreach ($extractions as $extraction)
          <div class="mb-4 p-4 rounded-lg">
            <p class="text-gray-800 dark:text-gray-300">タイトル: {{ $extraction->upload->title }}</p>
            <p class="text-gray-600 dark:text-gray-400 text-sm">抽出開始: {{ $extraction->start }}</p>
            <p class="text-gray-600 dark:text-gray-400 text-sm">抽出終了: {{ $extraction->end }}</p>
            <a href="{{ route('extractions.show', $extraction) }}" class="text-blue-500 hover:text-blue-700">詳細を見る</a>

            <a href="{{ route('memos.create', $extraction) }}" class="ml-4 text-blue-500 hover:text-blue-700">メモ作成</a>


          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div> --}}

  <div class="py-12 px-4">
    @foreach ($extractions as $extraction)
    <div class="flex max-w-md mx-auto overflow-hidden rounded-lg shadow-lg mb-4" style="box-shadow: 0px 0px 30px 10px rgb(255 255 255 / 80%);">
        <div class="w-1/3  bg-no-repeat bg-contain bg-center" style="background-image: url('{{ asset('images/tsuki2.png') }}')"></div>
    
        <div class="w-2/3 p-4 md:p-4">
            <p class="text-xm font-bold text-white">{{ $extraction->upload->title }}</p>
            <p class="text-white text-sm">開始: {{ $extraction->start }}</p>
            <p class="text-white text-sm">終了: {{ $extraction->end }}</p>

            <div class="flex justify-between mt-3 item-center">
                <a href="{{ route('extractions.show', $extraction) }}" class="text-sm hover:text-gray-200">詳細を見る</a>
            
            </div>
        </div>
    </div>
    @endforeach
  </div>

</x-app-layout>