<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tournament Application Submitted</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; background:#f8fafc; padding:20px;">

    <div style="max-width:600px; margin:0 auto; background:#ffffff; padding:20px; border-radius:8px;">

        {{-- 🎉 Header GIF --}}
        {{-- <div style="text-align:center; margin-bottom:20px;">
            <img 
                src="https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExb3o1aTN3aG9pOXJtM2Z0aHBzY3k3c2ZtZTFnZGR0dWx1Z2YxNHJ5ZCZlcD12MV9naWZzX3NlYXJjaCZjdD1n/l0MYt5jPR6QX5pnqM/giphy.gif"
                alt="Tournament Applied"
                style="max-width:100%; border-radius:6px;"
            >
        </div> --}}

        <h2 style="color:#0f172a;">🎉 Tournament Application Submitted!</h2>

        <p>
            Hi Gamer 👋,<br><br>
            Your application for the tournament has been successfully submitted on 
            <strong>{{ config('app.name') }}</strong>.
        </p>

        <hr>

        <p><strong>🏆 Tournament:</strong> {{ $tournament->title }}</p>
        <p><strong>🎮 Mode:</strong> {{ ucfirst($join->mode) }}</p>

        <hr>

        <h3 style="color:#16a34a;">🆔 Your Join Code</h3>

        <p style="
            font-size:20px;
            font-weight:bold;
            background:#f1f5f9;
            padding:10px;
            border-radius:6px;
            display:inline-block;
        ">
            {{ $joinCode }}
        </p>

        <p>
            <strong>Status:</strong>
            {{ $join->status === 'approved' ? 'Approved ✅' : 'Pending Approval ⏳' }}
        </p>

        <hr>

        <p style="color:#dc2626;">
            🔒 <strong>Important Security Notice</strong><br>
            This join code is linked to your tournament entry.  
            <strong>Please do not share it with anyone.</strong>  
            Sharing your join code may allow others to view or edit your team details.
        </p>

        <hr>

        <p>
            👉 You can view your tournament details and manage team members using the link below:
        </p>

        <p>
            <a 
                href="{{ route('join.code.index) }}"
                style="
                    display:inline-block;
                    padding:10px 16px;
                    background:#22c55e;
                    color:#ffffff;
                    text-decoration:none;
                    border-radius:6px;
                "
            >
                View Tournament Details
            </a>
        </p>

        <br>

        <p>
            If you have any questions, feel free to contact the tournament organizer through the platform.
        </p>

        <p style="margin-top:30px;">
            Thanks & good luck! 🎮🔥<br>
            <strong>{{ config('app.name') }} Team</strong>
        </p>

    </div>

</body>
</html>
