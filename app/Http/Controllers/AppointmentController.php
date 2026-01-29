<?php
namespace App\Http\Controllers;

use GuzzleHttp\Client;
use App\Models\Appointment;
use App\Models\PlantOfficial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{    
    public function getHolidays()
    {
        $client = new Client();
        $response = $client->request('GET', 'https://calendarific.com/api/v2/holidays', [
            'query' => [
                'api_key' => 'boTfkzyrpmRz6un1tPfJPCgxk97clNWa',
                'country' => 'CO',
                'year' => date('Y')
            ]
        ]);

        $holidays = json_decode($response->getBody()->getContents(), true);

        return $holidays['response']['holidays'];
    }

    public function showCalendar()
    {
        $appointments = Appointment::all();
        $holidays = $this->getHolidays();
        $holidayDates = array_map(function($holiday) {
            return $holiday['date']['iso'];
        }, $holidays);

        return view('public.citizen-care-services.appointments.calendar', [
            'appointments' => $appointments,
            'holidays' => $holidayDates,
        ]);
    }

    public function showForm(Request $request)
    {
        $employees = PlantOfficial::all();
        $selectedDate = $request->date;
        $selectedTime = $request->time;
        $assignedAppointments = Appointment::where('date', $selectedDate)->pluck('hour')->toArray();
        return view('public.citizen-care-services.appointments.form', compact('employees', 'selectedDate', 'selectedTime', 'assignedAppointments'));
    }

    public function store(Request $request)
    {
        $month = date('m', strtotime($request->date));
        $year = date('Y', strtotime($request->date));

        $userAppointments = Appointment::where('document_number', $request->document_number)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->count();

        if ($userAppointments >= 2) {
            return redirect()->back()->with('error', 'Ya has agendado el máximo de 2 citas este mes.');
        }

        $appointment = Appointment::create($request->all());
        Mail::raw("Tu cita ha sido programada para el día: " . $appointment->date . " a las " . $appointment->hour, function ($message) use ($appointment) {
            $message->to($appointment->email)
                    ->subject('Confirmación de Cita');
        });

        return redirect()->route('calendar')->with('success', 'Cita agendada con éxito.');
    }
}
