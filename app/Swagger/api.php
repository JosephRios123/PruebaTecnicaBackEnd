<?php

/**
 * @OA\Info(
 *     title="API de Reservas de Vuelos",
 *     version="1.0.0",
 *     description="API para gestionar reservas de vuelos y obtener información de aeropuertos y vuelos."
 * )
 */

/**
 * @OA\Schema(
 *     schema="Itinerary",
 *     type="object",
 *     @OA\Property(property="departureCity", type="string", example="MDE"),
 *     @OA\Property(property="arrivalCity", type="string", example="BOG"),
 *     @OA\Property(property="date", type="string", example="2024-10-30"),
 *     @OA\Property(property="hour", type="string", example="10:00")
 * )
 */

/**
 * @OA\Schema(
 *     schema="Flight",
 *     type="object",
 *     @OA\Property(property="dateOfDeparture", type="string", example="2024-02-09"),
 *     @OA\Property(property="timeOfDeparture", type="string", example="08:00"),
 *     @OA\Property(property="dateOfArrival", type="string", example="2024-02-09"),
 *     @OA\Property(property="timeOfArrival", type="string", example="10:00"),
 *     @OA\Property(property="marketingCarrier", type="string", example="AV"),
 *     @OA\Property(property="flightOrtrainNumber", type="string", example="AV123"),
 *     @OA\Property(property="locationId", type="object",
 *         @OA\Property(property="departureCity", type="string", example="MDE"),
 *         @OA\Property(property="arrivalCity", type="string", example="BOG")
 *     )
 * )
 */

/**
 * @OA\Schema(
 *     schema="Airport",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="city", type="string", example="Medellín"),
 *     @OA\Property(property="name", type="string", example="Aeropuerto José María Córdova"),
 *     @OA\Property(property="country", type="string", example="Colombia"),
 *     @OA\Property(property="iata", type="string", example="MDE")
 * )
 */

/**
 * @OA\Schema(
 *     schema="Reserve",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="currency", type="string", example="COP"),
 *     @OA\Property(property="qty_passengers", type="integer", example=1),
 *     @OA\Property(property="adult", type="integer", example=1),
 *     @OA\Property(property="child", type="integer", example=0),
 *     @OA\Property(property="baby", type="integer", example=0),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-30T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-30T10:00:00Z"),
 * )
 */



    /**
     * @OA\Post(
     *     path="/api/flights",
     *     summary="Obtener vuelos",
     *     tags={"Vuelos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"itinerary", "qtyPassengers", "adult", "searchs"},
     *             @OA\Property(property="itinerary", type="array", 
     *                 @OA\Items(ref="#/components/schemas/Itinerary")
     *             ),
     *             @OA\Property(property="qtyPassengers", type="integer", example=1),
     *             @OA\Property(property="adult", type="integer", example=1),
     *             @OA\Property(property="searchs", type="integer", example=1)
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vuelos recuperados exitosamente",
     *         @OA\JsonContent(type="object", 
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="array", 
     *                 @OA\Items(ref="#/components/schemas/Flight")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontraron vuelos disponibles",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="No se encontraron vuelos disponibles"))
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error en el servidor",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="Server error"))
     *     )
     * )
     */

    /**
     * @OA\Post(
     *     path="/api/reserve",
     *     summary="Guardar reservas",
     *     tags={"Reservas"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"currency", "qty_passengers", "adult", "itineraries"},
     *             @OA\Property(property="currency", type="string", example="COP"),
     *             @OA\Property(property="qty_passengers", type="integer", example=1),
     *             @OA\Property(property="adult", type="integer", example=1),
     *             @OA\Property(property="child", type="integer", example=0),
     *             @OA\Property(property="baby", type="integer", example=0),
     *             @OA\Property(property="itineraries", type="array", 
     *                 @OA\Items(ref="#/components/schemas/Itinerary")
     *             )
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Reservación guardada correctamente",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Reservación guardada correctamente"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="El campo itineraries es requerido.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error en el servidor",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="Server error"))
     *     )
     * )
     */

