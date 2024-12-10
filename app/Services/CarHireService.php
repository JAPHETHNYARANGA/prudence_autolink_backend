<?
namespace App\Services;

use App\Models\CarHire as ModelsCarHire;
use App\Models\CarHireImages;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class CarHireService
{
    /**
     * Validate car hire data.
     *
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public function validateCarHireData(array $data): array
    {
        $validator = Validator::make($data, [
            'make' => 'required|integer|exists:makes,id',
            'model' => 'required|integer|exists:models,id',
            'category' => 'required|integer|exists:categories,id',
            'year' => 'required|integer',
            'transmission' => 'required|string',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'frequency' => 'required|integer',
            'location' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Create a new car hire.
     *
     * @param array $validatedData
     * @param array|null $images
     * @return ModelsCarHire
     */
    public function createCarHire(array $validatedData, ?array $images): ModelsCarHire
    {
        $carHire = ModelsCarHire::create(array_merge($validatedData, ['user_id' => Auth::id()]));

        if ($images) {
            foreach ($images as $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('car_hire'), $filename);
                CarHireImages::create([
                    'car_id' => $carHire->id,
                    'url' => 'car_hire/' . $filename,
                ]);
            }
        }

        return $carHire;
    }

    /**
     * Retrieve all car hires with images.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllCarHires()
    {
        $carHires = ModelsCarHire::with('carHireImages')->get();

        $baseUrl = url('/');
        foreach ($carHires as $carHire) {
            foreach ($carHire->carHireImages as $image) {
                $image->url = $baseUrl . '/' . $image->url;
            }
        }

        return $carHires;
    }

    /**
     * Get a car hire by ID.
     *
     * @param int $id
     * @return ModelsCarHire|null
     */
    public function getCarHireById(int $id): ?ModelsCarHire
    {
        $carHire = ModelsCarHire::with('carHireImages')->find($id);
        if ($carHire) {
            $baseUrl = url('/');
            foreach ($carHire->carHireImages as $image) {
                $image->url = $baseUrl . '/' . $image->url;
            }
        }

        return $carHire;
    }

    /**
     * Update a car hire by ID.
     *
     * @param int $id
     * @param array $data
     * @return ModelsCarHire|null
     */
    public function updateCarHire(int $id, array $data): ?ModelsCarHire
    {
        $carHire = ModelsCarHire::find($id);
        if (!$carHire) {
            return null;
        }

        $carHire->update($data);
        return $carHire;
    }

    /**
     * Delete a car hire by ID.
     *
     * @param int $id
     * @return void
     */
    public function deleteCarHire(int $id)
    {
        $carHire = ModelsCarHire::find($id);
        if ($carHire) {
            // Delete associated images if necessary
            foreach ($carHire->carHireImages as $image) {
                $imagePath = public_path($image->url);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $image->delete();
            }
            $carHire->delete();
        }
    }
}
