<x-layouts::app title="Adicionar usuário">
    <div>
        new user
        <form action="{{ route('users.store') }}" method="POST">
            <flux:input name="name" label="Nome do usuário"/>
            <flux:input name="email" type="email" label="E-mail"/>
            <flux:input name="password" type="password" label="Senha"/>
            <flux:input name="password_confirmation" type="password" label="Confirmar senha"/>

            <div class="text-right">
                <flux:button type="submit" class="mt-6">Criar usuário</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>