<?php

namespace App\Actions\Fortify;

use App\Models\Farmstead;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'is_farmer' => 'required|boolean'
        ])->validate();

        return DB::transaction(function () use ($input) {
            return tap(User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]), function (User $user) use ($input) {
                // if ($input['is_farmer']) {
                //     $this->createFarmstead($user);
                // }
                // $this->createTeam($user);
            });
        });
    }

    protected  function createFarmstead(User $user): void {
        $farmstead = Farmstead::create([
            'name' => explode(' ', $user->name, 2)[0]."s Boerderij",
            'email' => $user->email,
            'phone_number' => null,
            'location' => null,
            'show_email' => false,
            'show_number' => false,
        ]);
        $user->farmstead = $farmstead->id;
    }
    /**
     * Create a personal team for the user.
     */
    protected function createTeam(User $user): void
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0]."'s Team",
            'personal_team' => true,
        ]));
    }
}
