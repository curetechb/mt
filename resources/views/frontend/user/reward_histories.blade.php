@extends('frontend.layouts.user_panel')

@section('panel_content')

<div class="row">
    <div class="col-md-4 mx-auto">
        <div class="bg-grad-1 text-white rounded-lg mb-4 overflow-hidden">
            <div class="px-3 pt-3">
                <div class="h3 fw-700">
                    {{ Auth::user()->points }} {{ translate('Point(s)') }}
                </div>
                <div class="opacity-50">{{ translate('in your Account') }}</div>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                <path fill="rgba(255,255,255,0.3)" fill-opacity="1" d="M0,192L30,208C60,224,120,256,180,245.3C240,235,300,181,360,144C420,107,480,85,540,96C600,107,660,149,720,154.7C780,160,840,128,900,117.3C960,107,1020,117,1080,112C1140,107,1200,85,1260,74.7C1320,64,1380,64,1410,64L1440,64L1440,320L1410,320C1380,320,1320,320,1260,320C1200,320,1140,320,1080,320C1020,320,960,320,900,320C840,320,780,320,720,320C660,320,600,320,540,320C480,320,420,320,360,320C300,320,240,320,180,320C120,320,60,320,30,320L0,320Z"></path>
            </svg>
        </div>
    </div>
</div>

<ul class="nav nav-pills mb-2">
    <li class="nav-item">
      <a class="nav-link @if(!request('type')) active @endif" aria-current="page" href="{{ route('reward_histories') }}">Reward Accrued</a>
    </li>
    <li class="nav-item ml-1">
      <a class="nav-link border-sm @if(request('type')) active @endif" href="{{ route('reward_histories', ['type' => 'redeem']) }}">Reward Redeem</a>
    </li>
</ul>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Reward Histories')}}</h5>
        </div>
          @if (!request('type'))
          <div class="card-body">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>{{ translate('SL')}}</th>
                        <th>{{ translate('Order ID')}}</th>
                        <th>{{ translate('Points')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($accrued as $key => $accr)
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>#{{ $accr->code }}</td>
                            <td class="text-success">+{{ $accr->points_accrued }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $accrued->links() }}
            </div>
        </div>
        @else
        <div class="card-body">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>{{ translate('SL')}}</th>
                        <th>{{ translate('Order ID')}}</th>
                        <th>{{ translate('Points')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($redeem as $key => $rede)
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>#{{ $rede->code }}</td>
                            <td class="text-danger">-{{ $rede->points_redeem }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $redeem->links() }}
            </div>
        </div>
          @endif
    </div>
@endsection

