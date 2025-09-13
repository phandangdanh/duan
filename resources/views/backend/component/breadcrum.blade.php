<!-- resources/views/backend/component/breadcrum.blade.php -->
<div class="breadcrum">
    <h1>{{ $title ?? 'Default Title' }}</h1>
    @if (!empty($action))
        <p>Action: {{ $action }}</p>
    @endif
</div>
