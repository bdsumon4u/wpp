<x-layout>
    <x-slot name="header">
        {{ __('Chats') }}
    </x-slot>

    <x-panel class="flex flex-col items-center pt-16 pb-16">
        <x-application-logo class="block w-auto h-12" />

        <div class="mt-8 text-2xl">
            <div class="col-sm-4" >
				<div class="form-group">
					<input type="text" data-target=".contact" class="form-control filter-row" placeholder="{{ __('Search....') }}">
				</div>
				<div class="d-flex justify-content-center qr-area">
					<div class="justify-content-center">
						&nbsp&nbsp
						<div class="spinner-grow text-primary" role="status">
							<span class="sr-only">{{ __('Loading...') }}</span> 
						</div>
						<br>
						<p><strong>{{ __('Loading Contacts.....') }}</strong></p>
					</div>

				</div>
				<div class="text-white alert bg-gradient-red server_disconnect none" role="alert">
					{{ __('Opps! Server Disconnected 😭') }}
				</div>
				<ul class="mt-5 list-group list-group-flush list my--3 contact-list position-relative">
                    @foreach ($chats as $key => $chat)
                        <li class="list-group-item px-0 contact contact{{$key}}">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <a href="javascript:void(0)" data-active=".contact{{$key}}" data-number="{{$chat['number']}}" class="ml-2 avatar rounded-circle wa-link">
                                        <img alt="" src="${whatsappicon}">
                                    </a>
                                </div>
                                <div class="col ml--2">
                                    <h4 class="mb-0">
                                        <a href="javascript:void(0)" data-active=".contact{{$key}}" class="wa-link" data-number="{{$chat['number']}}">+{{$chat['number']}}</a>
                                    </h4>
                                    {{-- {{$chat['timestamp']}} --}}
                                </div>
                            </div>
                        </li>
                    @endforeach
				</ul>
			</div>
			<div class="mt-5 col-sm-8" >
					<div class="text-center">
						<img src="{{ asset('assets/img/whatsapp-bg.png') }}" class="wa-bg-img">
					</div>
					<form method="post" class="ajaxform" action="{{ route('user.chat.send-message',$device->id) }}">
						@csrf
						<div class="mb-5 form-group none sendble-row">
							<label>{{ __('Receiver') }}</label>
							<input type="number" readonly="" name="receiver" value="" class="bg-white form-control reciver-number">
						</div>
						<div class="input-group none sendble-row wa-content-area" >
							
							<input type="text" name="message" class="form-control" id="plain-text" placeholder="Message" aria-label="Recipient's username" aria-describedby="basic-addon2">
							<div class="input-group-append">
								<button class="mr-3 btn btn-outline-success submit-button" type="submit"><i class="fi fi-rs-paper-plane"></i>&nbsp&nbsp {{ __('Sent') }}</button>
							</div>
						</div>
					</form>				
			</div>
        </div>
    </x-panel>
</x-layout>