<?php

namespace App\Http\Controllers;

use App\Http\Requests\AirportRequest;
use App\Models\Reserve;
use App\Models\Itinerary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
     *     @OA\Property(property="codigo IATA", type="string", example="MDE", description="Codigo IATA de la ciudad")
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
                'iata' => $city['new_airports'][0]['codeIataAirport'] ?? ''
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
        if (!$request->has('currency') || empty($request->input('currency'))) {
            return response()->json(['error' => 'El parámetro currency es requerido.'], 400);
        }

        if (!$request->has('itinerary') || !is_array($request->input('itinerary')) || empty($request->input('itinerary'))) {
            return response()->json(['error' => 'El parámetro itinerary es requerido y debe ser un array no vacío.'], 400);
        }

        // Validación del campo 'qtyPassengers' (debe ser un entero, no estar vacío y ser al menos 1)
        if (!$request->has('qtyPassengers') || !is_numeric($request->input('qtyPassengers')) || $request->input('qtyPassengers') < 1) {
            return response()->json(['error' => 'El parámetro qtyPassengers es requerido, debe ser un número entero y al menos 1.'], 400);
        }

        // Validación del campo 'adult' (debe ser un entero, no estar vacío y ser al menos 1)
        if (!$request->has('adult') || !is_numeric($request->input('adult')) || $request->input('adult') < 1) {
            return response()->json(['error' => 'El parámetro adult es requerido, debe ser un número entero y al menos 1.'], 400);
        }

        // Validación del campo 'searchs' (debe ser un entero, no estar vacío, y ser al menos 1)
        if (!$request->has('searchs') || !is_numeric($request->input('searchs')) || $request->input('searchs') < 1) {
            return response()->json(['error' => 'El parámetro searchs es requerido, debe ser un número entero y al menos 1.'], 400);
        }
        // Validar los parámetros de la solicitud
        $request->validate([
            'itinerary' => 'required|array',
            'qtyPassengers' => 'required|integer|min:1',
            'currency' => 'required|string',
            'adult' => 'required|integer|min:1',
            'searchs' => 'required|integer|min:1'
        ]);

        try {
            $apiUrl = "https://staging.travelflight.aiop.com.co/api";

            // Llamada a la API externa
            $response = Http::post($apiUrl . '/flights/v2', [
                'itinerary' => $request->input('itinerary'),
                'qtyPassengers' => $request->input('qtyPassengers'),
                'adult' => $request->input('adult'),
                'currency' => 'COP',
                'searchs' => $request->input('searchs') // Agregado para enviar el campo searchs
            ]);

            // Verificar si la respuesta de la API fue exitosa
            if ($response->failed()) {
                return response()->json(['error' => 'No se pudieron recuperar los datos de los vuelos', 'details' => $response->json()], 500);
            }

            // Obtener los datos de vuelos
            $flights = $response->json();

            // Asegúrate de que la respuesta contenga vuelos
            if (!isset($flights['data']['Seg1'])) {
                return response()->json(['error' => 'No se encontraron vuelos disponibles'], 404);
            }

            // Formatear la respuesta de los vuelos
            $formattedFlights = $this->formatFlightsResponse($flights['data']['Seg1'], $request->input('searchs'));

            return response()->json([
                'status' => 200,
                'data' => $formattedFlights
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
        }
    }

    private function formatFlightsResponse($flights, $searchs)
    {
        // Inicializar el arreglo de resultados
        $result = [];

        foreach ($flights as $flightSegment) {
            foreach ($flightSegment['segments'] as $flight) {
                // Limitar el número de resultados a lo que se especificó en searchs
                if (count($result) < $searchs) {
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
     *         @OA\Property(property="currency", type="string", example="COP", description="Moneda de la reserva"),
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
     *             required={"currency", "qty_passengers", "adult", "itineraries"},
     *             @OA\Property(property="currency", type="string", example="USD", description="Moneda de la reserva"),
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
     *                 @OA\Items(type="string", example="El parámetro currency es requerido."),
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

        if (!$request->has('currency') || empty($request->input('currency'))) {
            $errors[] = 'El parámetro currency es requerido.';
        }

        if (!$request->has('itineraries') || !is_array($request->input('itineraries')) || empty($request->input('itineraries'))) {
            $errors[] = 'El parámetro itineraries es requerido y debe ser un array no vacío.';
        }

        if (!$request->has('qty_passengers') || !is_numeric($request->input('qty_passengers')) || $request->input('qty_passengers') < 1) {
            $errors[] = 'El parámetro qtyPassengers es requerido, debe ser un número entero y al menos 1.';
        }

        if (!$request->has('adult') || !is_numeric($request->input('adult')) || $request->input('adult') < 1) {
            $errors[] = 'El parámetro adult es requerido, debe ser un número entero y al menos 1.';
        }

        // Si hay errores, retornarlos juntos
        if (count($errors) > 0) {
            return response()->json(['error' => $errors], 400);
        }

        // Crear la reserva
        $reserve = Reserve::create([
            'currency' => $request->input('currency'),
            'qty_passengers' => $request->input('qty_passengers'),
            'adult' => $request->input('adult'),
            'child' => $request->input('child') ?? 0,
            'baby' => $request->input('baby') ?? 0,
        ]);

        // Guardar itinerarios
        foreach ($request->input('itineraries') as $itinerary) {
            Itinerary::create([
                'reserve_id' => $reserve->id,
                'departure_city' => $itinerary['departure_city'],
                'arrival_city' => $itinerary['arrival_city'],
                'departure_hour' => $itinerary['departure_hour'],
            ]);
        }

        return response()->json(['message' => 'Reservación guardada correctamente'], 201);
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
