<!DOCTYPE html>
<html>
<head>
    <title>Nouveau membre ajouté au groupe</title>
</head>
<body>
    <h1>Bonjour,</h1>
    <p>Un nouvel utilisateur, {{ $user }}, a rejoint votre groupe "{{ $group }}".</p>
    <p>Connectez-vous pour l'accueillir et commencer à collaborer.</p>
    <p><a href="">Voir le groupe</a></p>
    {{-- {{ route('groups.show', $group->id) }} --}}
</body>
</html>
