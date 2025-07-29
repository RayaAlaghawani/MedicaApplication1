@component('mail::message')
    <h1>We have recieved your request your to reset your account password</h1>
    <p>You Can use the following code to recover your account :</p>

    @component ('mail::panel')
        {{$code}}
    @endcomponent
    <p>allowed duration of the code is one hour from the time the message we sent </p>
@endcomponent
