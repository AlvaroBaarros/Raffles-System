<?php

namespace App\Console\Commands;

use App\Models\Participante;
use App\Models\Raffle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class LiberarReservasExpiradas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservas:expiradas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Liberando numeros de reservas expiradas';

    public function handle()
    {
        $total = self::liberar();

        $this->info($total . ' reserva(s) expirada(s) liberada(s).');
    }

    /**
     * Libera as reservas nao pagas cujo prazo acabou.
     * Cada liberacao usa trava na rifa (ver Participante::liberarReserva), entao nao
     * conflita com compras acontecendo ao mesmo tempo e nunca libera compra paga.
     */
    public static function liberar()
    {
        $ids = [];

        // Estrutura antiga / fazendinha: numeros reservados na tabela raffles
        $reservas = Raffle::select('participant_id')
            ->where('status', '=', 'Reservado')
            ->whereNotNull('participant_id')
            ->groupBy('participant_id')
            ->get();

        foreach ($reservas as $reserva) {
            $ids[$reserva->participant_id] = true;
        }

        // Estrutura nova: contador de reservados no participante
        foreach (Participante::where('reservados', '>', 0)->pluck('id') as $id) {
            $ids[$id] = true;
        }

        $liberadas = 0;
        foreach (array_keys($ids) as $id) {
            try {
                $participante = Participante::find($id);
                if ($participante && $participante->reservaExpirada() && $participante->liberarReserva()) {
                    $liberadas++;
                }
            } catch (\Throwable $th) {
                Log::error('Falha ao liberar reserva ' . $id . ': ' . $th->getMessage());
            }
        }

        return $liberadas;
    }
}
