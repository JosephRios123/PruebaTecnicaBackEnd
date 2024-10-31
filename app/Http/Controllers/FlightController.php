<?php

namespace App\Http\Controllers;

use App\Models\Reserve;
use App\Models\Itinerary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


/**
 * @OA\Info(
 *     title="Prueba Técnica: Sistema de Búsqueda de Vuelos",
 *     version="1.0.0",
 *     description="API para gestionar reservas de vuelos y obtener información de aeropuertos y vuelos."
 * )
 */

class FlightController extends Controller
{

    /**
     * @OA\Schema(
     *     schema="Airport",
     *     type="object",
     *     title="Airport",
     *     description="Modelo de aeropuerto",
     *     @OA\Property(property="ciudad", type="string", example="Medellin", description="Ciudad del aeropuerto"),
     *     @OA\Property(property="nombre", type="string", example="Jose Maria Cordova", description="Nombre del aeropuerto"),
     *     @OA\Property(property="país", type="string", example="Colombia", description="Nombre del país"),
     *     @OA\Property(property="codigo IATA", type="string", example="MDE", description="Codigo IATA de la ciudad"),
     * )
     * 
     * @OA\Post(
     *     path="/api/airports",
     *     summary="Obtener aeropuertos",
     *     tags={"Aeropuertos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code"},
     *             @OA\Property(property="code", type="string", example="medellin")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Aeropuertos recuperados exitosamente",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Airport"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="El parámetro code es requerido.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error en el servidor",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="Server error"))
     *     )
     * )
     */


    public function obtenerAeropuertos(Request $request)
    {
        if (!$request->has('code') || empty($request->input('code'))) {
            return response()->json(['error' => 'El parámetro code es requerido.'], 400);
        }

        try {
            $apiUrl = "https://staging.travelflight.aiop.com.co/api";
            $response = Http::post($apiUrl . '/airports/v2', [
                'code' => $request->input('code')
            ]);

            if ($response->failed()) {
                return response()->json(['error' => 'No se pudieron recuperar datos de la API externa'], 500);
            }

            $airportsData = $response->json();
            $formattedAirports = $this->formatAirportsResponse($airportsData);

            return response()->json($formattedAirports, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    private function formatAirportsResponse($airports)
    {
        // Comprobar si hay ciudades disponibles
        if (empty($airports['cities'])) {
            return []; // Retornar un array vacío si no hay ciudades
        }

        return array_map(function ($city) {
            return [
                'id' => $city['new_airports'][0]['airportId'] ?? null,
                'city' => $city['nameCity'] ?? '',
                'name' => $city['new_airports'][0]['nameAirport'] ?? '',
                'country' => $city['new_country']['nameCountry'] ?? '',
                'iata' => $city['new_airports'][0]['codeIataAirport'] ?? '',
            ];
        }, $airports['cities']);
    }


    /**
     * @OA\Schema(
     *     schema="Flight",
     *     type="object",
     *     title="Flight",
     *     description="Modelo de vuelo",
     *     properties={
     *         @OA\Property(property="dateOfDeparture", type="string", example="2024-11-20", description="Fecha de salida"),
     *         @OA\Property(property="timeOfDeparture", type="string", example="10:30", description="Hora de salida"),
     *         @OA\Property(property="dateOfArrival", type="string", example="2024-11-20", description="Fecha de llegada"),
     *         @OA\Property(property="timeOfArrival", type="string", example="14:45", description="Hora de llegada"),
     *         @OA\Property(property="marketingCarrier", type="string", example="AA", description="Código de la aerolínea"),
     *         @OA\Property(property="flightOrtrainNumber", type="string", example="1234", description="Número de vuelo"),
     *         @OA\Property(property="locationId", type="object",
     *             properties={
     *                 @OA\Property(property="departureCity", type="string", example="BOG", description="Ciudad de salida"),
     *                 @OA\Property(property="arrivalCity", type="string", example="MIA", description="Ciudad de llegada")
     *             }
     *         )
     *     }
     * )
     * 
     * 
     * @OA\Post(
     *     path="/api/flights",
     *     summary="Obtener vuelos",
     *     tags={"Vuelos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"itinerary", "qtyPassengers", "adult", "searchs"},
     *             @OA\Property(property="qtyPassengers", type="integer", example=4, description="Número total de pasajeros"),
     *             @OA\Property(property="adult", type="integer", example=4, description="Número de adultos"),
     *             @OA\Property(property="child", type="integer", example=0, description="Número de niños"),
     *             @OA\Property(property="baby", type="integer", example=0, description="Número de bebés"),
     *             @OA\Property(property="searchs", type="integer", example=2, description="Número de búsquedas a realizar"),
     *             @OA\Property(property="arolinea", type="string", example="JA", description="Nombre de la Aerolinea"),
     *             @OA\Property(property="itinerary", type="array", 
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="departureCity", type="string", example="MDE", description="Ciudad de salida"),
     *                     @OA\Property(property="arrivalCity", type="string", example="BOG", description="Ciudad de llegada"),
     *                     @OA\Property(property="hour", type="string", example="2024-10-25T10:00:00", description="Hora de salida")
     *                 )
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vuelos recuperados exitosamente",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Flight"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="El parámetro itinerary es requerido.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error en el servidor",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="Server error"))
     *     )
     * )
     */


    public function obtenerVuelos(Request $request)
    {
        // Validación del campo 'qtyPassengers' (debe ser un entero, no estar vacío y ser al menos 1)
        if (!$request->has('qtyPassengers') || !is_numeric($request->input('qtyPassengers')) || $request->input('qtyPassengers') < 1) {
            return response()->json(['error' => 'El campo de cantidad de pasajeros (qtyPassengers) es requerido, debe ser un número entero y al menos 1.'], 400);
        }

        // Validación del campo 'adult' (debe ser un entero, no estar vacío y ser al menos 1)
        if (!$request->has('adult') || !is_numeric($request->input('adult')) || $request->input('adult') < 1) {
            return response()->json(['error' => 'El campo de cantidad de adultos (adult) es requerido, debe ser un número entero y al menos 1.'], 400);
        }

        // Validación adicional para la suma de pasajeros
        $child = $request->input('child') ?? 0;
        $baby = $request->input('baby') ?? 0;
        $totalPassengers = $request->input('adult') + $child + $baby;

        if ($totalPassengers !== (int)$request->input('qtyPassengers')) {
            return response()->json(['error' => 'La cantidad total de pasajeros debe ser la suma de los adultos, niños y bebés'], 400);
        }

        // Validar los parámetros de la solicitud
        $request->validate([
            'qtyPassengers' => 'required|integer|min:1',
            'adult' => 'required|integer|min:1',
            'child' => 'nullable|integer|min:0', // Permitir que 'child' sea opcional y mínimo 0
            'baby' => 'nullable|integer|min:0', // Permitir que 'baby' sea opcional y mínimo 0
            'itinerary' => 'required|array',
        ]);

        try {
            $apiUrl = "https://staging.travelflight.aiop.com.co/api";

            // Llamada a la API externa
            $response = Http::post($apiUrl . '/flights/v2', [
                'qtyPassengers' => $totalPassengers,
                'adult' => $request->input('adult'),
                'child' => $child,
                'baby' => $baby,
                'itinerary' => $request->input('itinerary'),
            ]);

            // Verificar si la respuesta de la API fue exitosa
            if ($response->failed()) {
                return response()->json(['error' => 'No se pudieron recuperar los datos de los vuelos o No hay registros que coincidan con los parámetros proporcionados.', 'details' => $response->json()], 500);
            }

            // Obtener los datos de vuelos
            $flights = $response->json();

            // Formatear la respuesta de los vuelos
            $formattedFlights = $this->formatFlightsResponse($flights['data']['Seg1']);

            return response()->json([
                'status' => 200,
                'data' => $formattedFlights
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
        }
    }

    private function formatFlightsResponse($flights)
    {
        // Inicializar el arreglo de resultados
        $result = [];

        foreach ($flights as $flightSegment) {
            foreach ($flightSegment['segments'] as $flight) {
                $result[] = [
                    'dateOfDeparture' => $flight['productDateTime']['dateOfDeparture'] ?? '',
                    'timeOfDeparture' => $flight['productDateTime']['timeOfDeparture'] ?? '',
                    'dateOfArrival' => $flight['productDateTime']['dateOfArrival'] ?? '',
                    'timeOfArrival' => $flight['productDateTime']['timeOfArrival'] ?? '',
                    'marketingCarrier' => $flight['companyId']['marketingCarrier'] ?? '',
                    'flightOrtrainNumber' => $flight['flightOrtrainNumber'] ?? '',
                    'locationId' => [
                        'departureCity' => $flight['location'][0]['locationId'] ?? '',
                        'arrivalCity' => $flight['location'][1]['locationId'] ?? ''
                    ]
                ];
            }
        }

        return $result; // Devuelve solo los vuelos formateados que necesitas
    }




    /**
     * @OA\Schema(
     *     schema="Reserve",
     *     type="object",
     *     title="Reserve",
     *     description="Modelo de reserva",
     *     properties={
     *         @OA\Property(property="qty_passengers", type="integer", example=3, description="Cantidad de pasajeros"),
     *         @OA\Property(property="adult", type="integer", example=2, description="Cantidad de adultos"),
     *         @OA\Property(property="child", type="integer", example=1, description="Cantidad de niños"),
     *         @OA\Property(property="baby", type="integer", example=0, description="Cantidad de bebés")
     *     }
     * )
     *
     * @OA\Schema(
     *     schema="Itinerary",
     *     type="object",
     *     title="Itinerary",
     *     description="Modelo de itinerario",
     *     properties={
     *         @OA\Property(property="departure_city", type="string", example="BOG", description="Ciudad de salida"),
     *         @OA\Property(property="arrival_city", type="string", example="MIA", description="Ciudad de llegada"),
     *         @OA\Property(property="departure_hour", type="string", example="2024-10-29T05:00:00.000Z", description="Hora de salida")
     *     }
     * )
     * 
     * @OA\Post(
     *     path="/api/reserve",
     *     summary="Guardar una reserva",
     *     tags={"Reservas"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"qty_passengers", "adult", "itineraries"},
     *             @OA\Property(property="qty_passengers", type="integer", example=3, description="Cantidad de pasajeros"),
     *             @OA\Property(property="adult", type="integer", example=2, description="Cantidad de adultos"),
     *             @OA\Property(property="child", type="integer", example=1, description="Cantidad de niños"),
     *             @OA\Property(property="baby", type="integer", example=0, description="Cantidad de bebés"),
     *             @OA\Property(
     *                 property="itineraries",
     *                 type="array",
     *                 description="Arreglo de itinerarios",
     *                 @OA\Items(ref="#/components/schemas/Itinerary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Reservación guardada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Reservación guardada correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="array", 
     *                 @OA\Items(type="string", example="El parámetro qtyPassengers es requerido, debe ser un número entero y al menos 1."),
     *                 @OA\Items(type="string", example="El parámetro adult es requerido, debe ser un número entero y al menos 1."),
     *                 @OA\Items(type="string", example="El parámetro itineraries es requerido y debe ser un array no vacío.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error en el servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Server error")
     *         )
     *     )
     * )
     */

    public function guardarReservas(Request $request)
    {
        $errors = [];

        // Validación usando el validador de Laravel
        $validator = Validator::make($request->all(), [
            'itineraries' => 'required|array|min:1',
            'qty_passengers' => 'required|integer|min:1',
            'adult' => 'required|integer|min:1',
            'child' => 'nullable|integer|min:0',
            'baby' => 'nullable|integer|min:0',
            'itineraries.*.departureCity' => 'required|string|size:3',
            'itineraries.*.arrivalCity' => 'required|string|size:3',
            'itineraries.*.hour' => 'required|date',
        ]);

        // Verificar si las validaciones fallaron
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        // Validación adicional para la suma de pasajeros
        $child = $request->input('child', 0);
        $baby = $request->input('baby', 0);
        $totalPassengers = $request->input('adult') + $child + $baby;

        if ($totalPassengers !== (int) $request->input('qty_passengers')) {
            $errors[] = 'La cantidad total de pasajeros debe ser la suma de los adultos, niños y bebés.';
        }

        // Validación de fecha de itinerarios
        $itineraries = $request->input('itineraries');
        foreach ($itineraries as $itinerary) {
            try {
                $departureDate = new \DateTime($itinerary['hour']);
                $currentDate = new \DateTime();

                if ($departureDate < $currentDate) {
                    $errors[] = 'La fecha de salida no puede ser anterior a la fecha actual.';
                    break; // Salir del bucle si se encuentra un error
                }
            } catch (\Exception $e) {
                $errors[] = 'Formato de fecha inválido en el itinerario.';
                break;
            }
        }

        // Si hay errores, retornarlos juntos
        if (count($errors) > 0) {
            return response()->json(['error' => $errors], 400);
        }

        try {
            // Crear la reserva
            $reserve = Reserve::create([
                'qty_passengers' => $request->input('qty_passengers'),
                'adult' => $request->input('adult'),
                'child' => $child,
                'baby' => $baby,
            ]);

            // Guardar itinerarios asociados a la reserva
            foreach ($itineraries as $itinerary) {
                Itinerary::create([
                    'reserve_id' => $reserve->id,
                    'departure_city' => $itinerary['departureCity'],
                    'arrival_city' => $itinerary['arrivalCity'],
                    'departure_hour' => $itinerary['hour'],
                ]);
            }

            return response()->json(['message' => 'Reservación guardada correctamente. Mira tus reservas!!'], 201);
        } catch (\Exception $e) {
            // Manejar errores de base de datos o excepciones generales
            return response()->json(['error' => 'Ocurrió un error al guardar la reserva. Inténtelo de nuevo.'], 500);
        }
    }




    /**
     * @OA\Get(
     *     path="/api/obtenerReservas",
     *     summary="Obtener todas las reservas",
     *     tags={"Reservas"},
     *     @OA\Response(
     *         response=200,
     *         description="Reservas recuperadas exitosamente",
     *         @OA\JsonContent(type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1, description="ID de la reserva"),
     *                 @OA\Property(property="currency", type="string", example="COP", description="Moneda de la reserva"),
     *                 @OA\Property(property="qty_passengers", type="integer", example=2, description="Número total de pasajeros"),
     *                 @OA\Property(property="adult", type="integer", example=1, description="Número de adultos"),
     *                 @OA\Property(property="child", type="integer", example=1, description="Número de niños"),
     *                 @OA\Property(property="baby", type="integer", example=0, description="Número de bebés"),
     *                 @OA\Property(property="itineraries", type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="departure_city", type="string", example="MDE", description="Ciudad de salida"),
     *                         @OA\Property(property="arrival_city", type="string", example="BOG", description="Ciudad de llegada"),
     *                         @OA\Property(property="departure_hour", type="string", example="2024-10-25T10:00:00", description="Hora de salida")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error en el servidor",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="Server error"))
     *     )
     * )
     */

    public function obtenerReservas(Request $request)
    {
        // Puedes validar los parámetros de búsqueda, si es necesario
        $reservas = Reserve::with('itineraries')->get(); // Recupera todas las reservas con sus itinerarios

        return response()->json($reservas, 200);
    }
}
