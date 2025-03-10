@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="aiz-titlebar mb-4">
        <div class="h6 fw-700">
            <a href="{{ route('preorder-product.details', $conversation->preorderProduct->product_slug) }}" class="fs-14">{{ $conversation->preorderProduct->getTranslation('product_name') }}</a>
        </div>
    </div>
    <div class="card rounded-0 shadow-none border">
        <div class="card-header bg-light">
            <div>
                <!-- Conversation title -->
                <h5 class="card-title fs-14 fw-700 mb-1">{{ $conversation->title }}</h5>
                <!-- Conversation Woth -->
                <p class="mb-0 fs-14 text-secondary fw-400">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" class="mr-2">
                        <g id="Group_24976" data-name="Group 24976" transform="translate(1053.151 256.688)">
                            <path id="Path_3012" data-name="Path 3012" d="M134.849,88.312h-8a2,2,0,0,0-2,2v5a2,2,0,0,0,2,2v3l2.4-3h5.6a2,2,0,0,0,2-2v-5a2,2,0,0,0-2-2m1,7a1,1,0,0,1-1,1h-8a1,1,0,0,1-1-1v-5a1,1,0,0,1,1-1h8a1,1,0,0,1,1,1Z" transform="translate(-1178 -341)" fill="#b5b5bf"/>
                            <path id="Path_3013" data-name="Path 3013" d="M134.849,81.312h8a1,1,0,0,1,1,1v5a1,1,0,0,1-1,1h-.5a.5.5,0,0,0,0,1h.5a2,2,0,0,0,2-2v-5a2,2,0,0,0-2-2h-8a2,2,0,0,0-2,2v.5a.5.5,0,0,0,1,0v-.5a1,1,0,0,1,1-1" transform="translate(-1182 -337)" fill="#b5b5bf"/>
                            <path id="Path_3014" data-name="Path 3014" d="M131.349,93.312h5a.5.5,0,0,1,0,1h-5a.5.5,0,0,1,0-1" transform="translate(-1181 -343.5)" fill="#b5b5bf"/>
                            <path id="Path_3015" data-name="Path 3015" d="M131.349,99.312h5a.5.5,0,1,1,0,1h-5a.5.5,0,1,1,0-1" transform="translate(-1181 -346.5)" fill="#b5b5bf"/>
                        </g>
                    </svg>
                    {{ translate('Conversations  Between you and') }}
                    @if(in_array($conversation->receiver->user_type, ['admin', 'staff']))
                        {{ get_setting('site_name') }}
                    @else
                        <a href="{{ route('shop.visit', $conversation->receiver->shop->slug) }}" class="">{{ $conversation->receiver->shop->name }}</a>
                    @endif
                </p>
            </div>
        </div>

        <div class="card-body">
            <!-- Conversations -->
            <div id="messages">
                @include('preorder.common.messages', ['conversation', $conversation])
            </div>

            <!-- Send message -->
            <form class="pt-4" action="{{ route('preorder-conversations.customer_reply') }}" method="POST">
                @csrf
                <input type="hidden" name="conversation_thread_id" value="{{ $conversation->id }}">
                <div class="form-group">
                    <textarea class="form-control rounded-0" rows="4" name="message" placeholder="{{ translate('Type your reply') }}" required></textarea>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary rounded-0 w-150px">{{ translate('Send') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
    function refresh_messages(){
        $.post('{{ route('preorder.conversations.refresh') }}', {_token:'{{ @csrf_token() }}', id:'{{ encrypt($conversation->id) }}'}, function(data){
            $('#messages').html(data);
        })
    }

    refresh_messages(); // This will run on page load
    setInterval(function(){
        refresh_messages() // this will run after every 4 seconds
    }, 5000);
    </script>
@endsection
