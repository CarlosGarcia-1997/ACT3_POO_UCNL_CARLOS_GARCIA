<?php

// 1. CLASE BASE (Padre)
class Personaje {
    public string $Nombre;
    public float $Vida; // Unificamos VidaJugador y VidaBoss en un solo atributo

    // Constructor de la clase base
    public function __construct(string $nombre, float $vida) {
        $this->Nombre = $nombre;
        $this->Vida = $vida;
    }

    // Método general que las clases hijas van a heredar
    public function RecibirDanio(float $danio) {
        $this->Vida -= $danio;
        $this->Vida = max($this->Vida, 0); // La vida no baja de 0
    }
}

// 2. CLASE DERIVADA (Hija 1)
class Jugador extends Personaje {
    public int $Mana = 100;
    public int $Pociones = 5;      
    public int $PocionesMana = 5;  
    public static int $JugadoresVivos = 0;

    public function __construct(string $nombre) {
        // Llamada al constructor de la clase base (Requisito de la rúbrica)
        parent::__construct($nombre, 100); 
        self::$JugadoresVivos++;
    }

    // Métodos exclusivos de la subclase
    public function AtaqueBasico(Personaje $objetivo) {
        echo "[{$this->Nombre}] ataca a {$objetivo->Nombre} con su espada.\n";
        $objetivo->RecibirDanio(50);
    }

    public function GolpeHeroico(Personaje $objetivo) {
        if ($this->Mana >= 20) {
            echo "[{$this->Nombre}] usa GOLPE HEROICO! Un ataque devastador.\n";
            $this->Mana -= 20; 
            $objetivo->RecibirDanio(100);
        } else {
            echo "[{$this->Nombre}] intenta usar Golpe Heroico pero NO TIENE MANÁ.\n";
        }
    }

    public function UsarPocion() {
        if ($this->Pociones > 0) {
            $this->Vida += 75;
            $this->Vida = min($this->Vida, 100); 
            $this->Pociones--;
            echo "[{$this->Nombre}] se bebe una poción verde. (Vida actual: {$this->Vida})\n";
        }
    }

    public function UsarPocionMana() {
        if ($this->PocionesMana > 0) {
            $this->Mana += 35; 
            $this->PocionesMana--;
            echo "[{$this->Nombre}] se bebe una poción azul. (Maná actual: {$this->Mana})\n";
        }
    }
}

// 3. CLASE DERIVADA (Hija 2)
class Boss extends Personaje {
    private static float $VidaBossOriginal = 500;
    public bool $Rage = false;

    public function __construct(string $nombre) {
        // Llamada al constructor de la clase base
        parent::__construct($nombre, self::$VidaBossOriginal);
    }

    // Sobrescritura de método (Requisito de la rúbrica): 
    // El Boss modifica cómo recibe daño si está en Rage
    public function RecibirDanio(float $danio) {
        if ($this->Rage) {
            $danio = $danio / 2; // Reduce el daño a la mitad
        }
        // Llama al método original de la clase padre con el daño modificado
        parent::RecibirDanio($danio); 
    }

    // Método exclusivo del Boss
    public function Fase2() {
        echo "{$this->Nombre} entra en rage y se cura a tope\n";
        $this->Rage = true;
        $this->Vida = self::$VidaBossOriginal;
    }
}

// (La clase Raid se queda exactamente igual)
class Raid {
    public static int $intentosTotales = 0;
    public static int $bossesDerrotados = 0;

    public static function NuevoIntento() {
        self::$intentosTotales++;
    }

    public static function BossDerrotado() {
        self::$bossesDerrotados++;
    }
}
?>