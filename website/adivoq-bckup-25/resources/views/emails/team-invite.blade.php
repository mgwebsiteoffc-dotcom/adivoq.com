<!DOCTYPE html>
<html>
<body>
    <h2>You're invited to join {{ config('app.name') }}</h2>
    <p>
        Hi, <br>
        You have been invited to join the team as <strong>{{ ucfirst($teamMember->role) }}</strong>.
    </p>
    <p>
        Click the link below to accept your invitation and set your password:
    </p>
    <p>
      <a href="{{ $inviteUrl }}">{{ $inviteUrl }}</a>
    </p>
    <p>
        If you did not expect this, you can ignore this email.
    </p>
</body>
</html>