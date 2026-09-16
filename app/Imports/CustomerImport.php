<?php

namespace App\Imports;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        

       /*  foreach ($rows as $row)
        {
          
            $users = User:: select('*')->where('email', $row['email'])->get();
           
        
            if($users){

                $users->update([
                    'nom' => $row['nom'],
                    'prenom' => $row['prenom'],
                    'email' => $row['email'],
                    'phone' => $row['phone'],
                    'password' => Hash::make($row['phone']),
                ]);

            }else{

                User::create([
                    'nom' => $row['nom'],
                    'prenom' => $row['prenom'],
                    'email' => $row['email'],
                    'phone' => $row['phone'],
                    'password' => Hash::make($row['phone']),
                ]);
            }

        } */

        foreach ($rows as $row)
    {

         if (Arr::has($row, ['email', 'nom', 'prenom', 'phone'])) { 
      
           // $user = User::where('email', $row['email'])->get();

           
                User::create([
                    
                    'nom' => $row['nom'],
                    'prenom' => $row['prenom'],
                    'email' => $row['email'],
                    'phone' => $row['phone'],
                    'password' => Hash::make($row['phone']),
                ]);
            
        } else {
          
            Log::warning('Row missing necessary keys: ', $row->toArray());
        }
    }
    }
}