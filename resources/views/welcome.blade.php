<x-guest-layout>
    <div class="flex min-h-screen">
        <div class="flex-1 flex justify-center items-center">
            <div class="w-80 max-w-80 space-y-6">
                <div class="flex justify-center opacity-50">
                    <a href="/" class="group flex items-center gap-3">
                        <div>
                            🕹️
                        </div>
    
                        <span class="text-xl font-semibold text-zinc-800 dark:text-white">joystickjury</span>
                    </a>
                </div>
    
                <flux:heading class="text-center" size="xl">Welcome back</flux:heading>
    
                <div class="space-y-4">
                    <flux:button class="w-full" href="{{ route('twitch.login') }}">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-twitch" viewBox="0 0 16 16">
                                <path d="M3.857 0 1 2.857v10.286h3.429V16l2.857-2.857H9.57L14.714 8V0zm9.714 7.429-2.285 2.285H9l-2 2v-2H4.429V1.143h9.142z"/>
                                <path d="M11.857 3.143h-1.143V6.57h1.143zm-3.143 0H7.571V6.57h1.143z"/>
                              </svg>
                        </x-slot>
                        Continue with Twitch
                    </flux:button>
                </div>
            </div>
        </div>
    
        <div class="flex-1 p-4 max-lg:hidden">
            <div class="text-white relative rounded-lg h-full w-full bg-zinc-900 flex flex-col items-start justify-end p-16" style="background-image: url('bg.png'); background-size: cover">
                <div class="flex gap-2 mb-4">
                    <flux:icon.star variant="solid" />
                    <flux:icon.star variant="solid" />
                    <flux:icon.star variant="solid" />
                    <flux:icon.star variant="solid" />
                    <flux:icon.star variant="solid" />
                </div>
    
                <div class="mb-6 italic font-base text-3xl xl:text-4xl">
                    joystickjury is the best way to coordinate game nights with your friends
                </div>
    
                <div class="flex gap-4">
                    <flux:avatar src="/kyle.png" class="size-12" />
    
                    <div class="flex flex-col justify-center font-medium">
                        <div class="text-lg">Kyle Evangelisto</div>
                        <div class="text-zinc-300">Creator of joystickjury 🤓</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>