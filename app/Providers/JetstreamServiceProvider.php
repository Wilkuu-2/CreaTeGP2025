<?php

namespace App\Providers;

use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\CreateTeam;
use App\Actions\Jetstream\DeleteTeam;
use App\Actions\Jetstream\DeleteUser;
use App\Actions\Jetstream\InviteTeamMember;
use App\Actions\Jetstream\RemoveTeamMember;
use App\Actions\Jetstream\UpdateTeamName;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePermissions();

        Jetstream::createTeamsUsing(CreateTeam::class);
        Jetstream::updateTeamNamesUsing(UpdateTeamName::class);
        Jetstream::addTeamMembersUsing(AddTeamMember::class);
        Jetstream::inviteTeamMembersUsing(InviteTeamMember::class);
        Jetstream::removeTeamMembersUsing(RemoveTeamMember::class);
        Jetstream::deleteTeamsUsing(DeleteTeam::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);
    }

    /**
     * Configure the roles and permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions([]);

        Jetstream::role('admin', 'Administrator', [
            'any:create',
            'any:read',
            'any:update',
            'any:delete',
        ])->description('Administrator users can perform any action.');

        Jetstream::role('organiser', 'Organiser', [
            'any:read',
            'org:read',
            'org:create',
            'org:update',
            'org:delete',
        ])->description('Organisers are people in the team that organise the initiative.');

        Jetstream::role('participant', 'Participant', [
            'org:read',
            'own:read',
            'own:write',
            'own:create',
            'own:update',
        ])->description('A participant is part of the group but cannot edit things bound to the team. They can edit their own data.');
    }
}
