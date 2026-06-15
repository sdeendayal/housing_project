<div class="to-block">
    <p><strong>To</strong></p>
    <p>Estate Officer / JE</p>
    <p>HSVP</p>
    <p><strong>{{ $profile['office_location'] }}</strong></p>
</div>

<p>
    Subject:- For issue the <strong>POSSESSION CERTIFICATE</strong> of Plot no
    <span class="pp-form-line">{{ $profile['plot_no'] }}</span>
    sector
    <span class="pp-form-line">{{ $profile['sector'] }}</span>
    urban estate
    <span class="pp-form-line">{{ $profile['urban_estate'] }}</span>.
</p>

<p><strong>Respected Sir/Madam,</strong></p>

<p class="pp-form-body">
    I/We the allottee/re-allottee the plot no
    <span class="pp-form-line">{{ $profile['plot_no'] }}</span>
    sector
    <span class="pp-form-line">{{ $profile['sector'] }}</span>
    urban estate
    <span class="pp-form-line">{{ $profile['urban_estate'] }}</span>.
    I/We want to request you kindly issue me/us the possession certificate of my/our above said plot no as soon as possible.
</p>

<div class="pp-form-closing">
    <p class="pp-form-thanks"><strong>Thanking you</strong></p>
    <div class="pp-form-signature">
        <p style="margin:0;"><strong>Yours sincerely</strong></p>
        <p class="pp-form-sign-name">{{ strtoupper($profile['name']) }}</p>
    </div>
</div>

<div class="pp-form-meta">
    Generated on {{ now()->format('d M Y') }} |
    Mobile: {{ $profile['mobile'] }}
    @if($profile['application_no']) | Application No: {{ $profile['application_no'] }} @endif
</div>
