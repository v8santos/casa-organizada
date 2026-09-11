<x-layouts::app title="Usuários">
    <div>
        <ul>
            @foreach ($users as $user)
                <li class="flex justify-between">
                    <div>{{ $user->name }}</div>
                    <div>{{ $user->email }}</div>
                </li>
            @endforeach
        </ul>
    </div>
</x-layouts::app>