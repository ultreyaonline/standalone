@extends('emails.layout_textonly')

{!! $message_text !!}

---

You are receiving this message because you are a member of the {{ config('site.community_acronym') }} community, having attended or served on a Tres Dias weekend with us.
We strive to keep our email volume to a minimum, and only send messages of relevance to community members.
You may update your email preferences, or unsubscribe, in your profile at {{ config('app.url') }}/me ; Note: if you unsubscribe you will miss out on important notices.
