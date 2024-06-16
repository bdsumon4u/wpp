<x-layout>
    <x-slot name="header">
        {{ __('Device') }}
    </x-slot>

    <x-panel class="flex flex-col items-center pt-16 pb-16">
        <x-application-logo class="block w-auto h-12" />

        <div class="mt-8 text-2xl">
            <div class="row justify-content-center">
                <div class="col-sm-8">
                    <div class="card card-neutral">
                        <div class="card-header">
                            <h4>{{ __('Scan the QR Code On Your Whatsapp Mobile App') }}</h4>
                            <div class="card-header-action none loggout_area">
                                <a href="javascript:void(0)" class="btn btn-sm btn-neutral logout-btn" data-id="{{ $device->id }}">
                                    <i class="fas fa-sign-out-alt"></i>&nbsp{{ __('Logout') }}
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-center qr-area">
                                <img src="{{ $device->qr }}" alt="QR Code">
                                
                                <div class="justify-content-center">
                                    &nbsp&nbsp
                                    <div class="spinner-grow text-primary" role="status">
                                        <span class="sr-only">{{ __('Loading...') }}</span> 
                                    </div>
                                    <br>
                                    <p><strong>{{ __('QR Loading.....') }}</strong></p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="text-white alert bg-gradient-red server_disconnect none" role="alert">
                                {{ __('Opps! Server Disconnected 😭') }}
                            </div>
                            
                            <div class="text-white alert bg-gradient-green logged-alert none" role="alert">
                                {{ __('Device Connected ') }} <img src="{{ asset('uploads/firework.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="card card-neutral none helper-box">
                        <div class="card-body">
                            <div class="row">
                                <div class="mt-2 mb-2 col-sm-6">
                                    <a href="{{ url('/user/device/chats/'.$device->id) }}" class="btn btn-neutral col-12">
                                        <i class="fi fi-rs-paper-plane"></i>&nbsp {{ __('My Chat list') }}
                                    </a>
                                </div>
                                <div class="mt-2 mb-2 col-sm-6">
                                    <a href="{{ url('/user/device/groups/'.$device->id) }}" class="btn btn-neutral col-12">
                                        <i class="fi fi-rs-paper-plane"></i>&nbsp {{ __('My Group list') }}
                                    </a>
                                </div>
            
                                <div class="mt-3 col-sm-6">
                                    <a href="{{ url('/user/sent-text-message') }}" class="btn btn-neutral col-12">
                                        <i class="fi fi-rs-paper-plane"></i>&nbsp {{ __('Send a message') }}
                                    </a>
                                </div>
                                <div class="mt-3 col-sm-6">
                                    <a href="{{ url('/user/bulk-message/create') }}" class="btn btn-neutral col-12">
                                        <i class="fi fi-rs-rocket-lunch"></i>&nbsp {{ __('Send bulk message') }}
                                    </a>
                                </div>
                                
                            </div>
                        </div>
                    </div>	
                </div>
                <div class="col-sm-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ __('How To Scan?') }}</h4>
                            <div class="card-header-action">
                                <a href="#" class="btn btn-sm btn-neutral">
                                    <i class="fas fa-lightbulb"></i>&nbsp{{ __('Guide') }}
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <img src="{{ asset('uploads/scan-demo.gif') }}" class="w-100" >
                        </div>
                        <div class="card-footer">
                            <div class="activities">
                        <div class="activity">
                            <div class="text-white activity-icon bg-primary shadow-primary">
                                <i class="ni ni-mobile-button"></i>
                            </div>
                            <div class="activity-detail">
                                <div class="mb-2">
                                    <span class="text-job text-primary">{{ __('Step 1') }}</span>
                                    <span class="bullet"></span>
                                </div>
                                <p>{{ __('Open WhatsApp on your phone') }}</p>
                            </div>
                        </div>
                        <div class="activity">
                            <div class="text-white activity-icon bg-primary shadow-primary">
                                <i class="ni ni-active-40"></i>
                            </div>
                            <div class="activity-detail">
                                <div class="mb-2">
                                    <span class="text-job text-primary">{{ __('Step 2') }}</span>
                                    <span class="bullet"></span>
                                </div>
                                <p>{{ __('Tap Menu or Settings and select Linked Devices') }}</p>
                            </div>
                        </div>
                        <div class="activity">
                            <div class="text-white activity-icon bg-primary shadow-primary">
                                <i class="ni ni-active-40"></i>
                            </div>
                            <div class="activity-detail">
                                <div class="mb-2">
                                    <span class="text-job text-primary">{{ __('Step 3') }}</span>
                                    <span class="bullet"></span>
                                </div>
                                <p>{{ __('Tap on Link a Device') }}</p>
                            </div>
                        </div>
                        <div class="activity">
                            <div class="text-white activity-icon bg-primary shadow-primary">
                                <i class="fa fa-qrcode"></i>
                            </div>
                            <div class="activity-detail">
                                <div class="mb-2">
                                    <span class="text-job text-primary">{{ __('Step 4') }}</span>
                                    <span class="bullet"></span>
                                </div>
                                <p>{{ __('Point your phone to this screen to capture the code') }}</p>
                            </div>
                        </div>
                    </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-panel>
</x-layout>