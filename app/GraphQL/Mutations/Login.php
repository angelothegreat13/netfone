<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final readonly class Login
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {   
        $validator = Validator::make($args, [
            'email' => 'required|email',
            'password' => 'required',
            'device' => 'required',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = User::where('email', $args['email'])->first();
    
        if (!$user || !Hash::check($args['password'], $user->password)) {
            throw new ValidationException([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user->createToken($args['device'])->plainTextToken;
    }
}
