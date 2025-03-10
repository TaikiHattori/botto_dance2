<div>
    <!-- Act only according to that maxim whereby you can, at the same time, will that it should become a universal law. - Immanuel Kant -->
</div>

<x-app-layout>
  {{-- <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('抽出') }}
    </h2>
  </x-slot> --}}

  <style>
    input {
      color: black;
    }

    .title {
      font-size: 30px;
    }

    .duration {
      font-size: 24px;
    }
  </style>

  <div class="py-12">
    <div class="max-w-md mx-auto sm:px-6 lg:px-8">
      <div class="overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">

          <p class="title">{{ $title }}</p>
          <br>

          <p class="duration">00:00 ~ {{ $duration }}</p>
          <br>

          <form action="{{ route('extractions.store') }}" method="POST">
            @csrf

            <div class="mb-4">
              <label for="start_time" class="block text-sm font-medium text-white-700">開始</label>
              <div class="flex">
              <input type="number" name="start_minu" id="start_minu" class="mt-1 block w-full rounded" required placeholder="分">
              <input type="number" name="start_sec" id="start_sec" class="mt-1 block w-full rounded" required placeholder="秒">
              </div>
            </div>

            <div class="mb-4">
              <label for="end_time" class="block text-sm font-medium text-white-700">終了</label>
              <div class="flex">
              <input type="number" name="end_minu" id="end_minu" class="mt-1 block w-full rounded" required placeholder="分">
              <input type="number" name="end_sec" id="end_sec" class="mt-1 block w-full rounded" required placeholder="秒">
              </div>
            </div>

            <input type="hidden" name="upload_id" value="{{ $upload->id }}">
            <div class="flex justify-end">
              <button type="submit" class="border-solid border border-white ml-4 hover:opacity-80 text-white font-bold py-2 px-4 rounded">抽出</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>