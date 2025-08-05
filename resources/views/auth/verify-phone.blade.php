<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verify Your Phone') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-8">
                <form method="POST" action="{{ route('auth.phone.verify') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="otp" class="block text-sm font-medium text-gray-700 mb-2">Enter the 6-digit OTP sent to your phone</label>
                        <input type="text" name="otp" id="otp" maxlength="6" class="w-full px-4 py-2 border rounded @error('otp') border-red-500 @enderror" required autofocus>
                        @error('otp')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">Verify</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
