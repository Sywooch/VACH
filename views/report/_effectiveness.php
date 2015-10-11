<?php

use yii\helpers\Html;
use app\models\Wheel;
?>
<h3>
    2 - Matrices de Efectividad Grupal y Organizacional
</h3>
<?php
if (count($groupRelationsMatrix) > 0) {
    echo $this->render('../dashboard/_number_matrix', [
        'data' => $groupRelationsMatrix,
        'members' => $members,
        'type' => Wheel::TYPE_GROUP,
        'memberId' => 0,
    ]);
}
?>
<?php
if (count($organizationalRelationsMatrix) > 0) {
    echo $this->render('../dashboard/_number_matrix', [
        'data' => $organizationalRelationsMatrix,
        'members' => $members,
        'type' => Wheel::TYPE_ORGANIZATIONAL,
        'memberId' => 0,
    ]);
}
?>
<h3>
    Descripción
</h3>
<p>
    La Matriz inserta en este ítem simboliza y mide la cantidad de respuestas que cada integrante da a los pedidos de sus pares, según la mirada del Equipo, sin tener en cuenta la Auto-Percepción de cada uno (“el cómo me veo”). En este orden, si yo creo que doy un 4, pero 15 personas que comparten conmigo el día a día me devuelven que doy un 2, evidentemente lo que estoy dando (proyectando) es un 2 y no un 4 (me vuelve lo que doy por la misma ley causa-efecto). Para este caso podríamos validar mi realidad como 2 y mi creencia como 4. Por consecuencia, en el caso de esta Matriz, podemos advertir entonces un Ranking de Responsabilidad, identificándolo numéricamente de mayor a menor, en función de los niveles de respuestas que el Equipo obtiene de cada uno de sus individuos.
</p>
<p>
    Vemos que si doy un 2 y creo que doy un 4, existirá una brecha (en términos de Conciencia) en cómo me veo y cómo me ven, y la diferencia entre esta dialéctica hará emerger el Grado de Conciencia que tengo de mí y el Grado de Conciencia La Matriz inserta en este ítem simboliza y mide la cantidad de respuestas que cada integrante da a los pedidos de sus pares, según la mirada del Equipo, sin tener en cuenta la Auto-Percepción de cada uno (“el cómo me veo”). En este orden, si yo creo que doy un 4, pero 15 personas que comparten conmigo el día a día me devuelven que doy un 2, evidentemente lo que estoy dando (proyectando) es un 2 y no un 4 (me vuelve lo que doy por la misma ley causa-efecto). Para este caso podríamos validar mi realidad como 2 y mi creencia como 4. Por consecuencia, en el caso de esta Matriz, podemos advertir entonces un Ranking de Responsabilidad, identificándolo numéricamente de mayor a menor, en función de los niveles de respuestas que el Equipo obtiene de cada uno de sus individuos.que el Grupo tiene de mí; por lo tanto, si hablamos de medir desempeño, de cómo me ven por ejemplo en Atención al Cliente, cuando esta competencia sea total, sí o sí, el grupo la podrá observar y me la devolverá tal como es. 
</p>
<p>
    Tal cual se puede observar en la tabla, esta brecha puede ser positiva o negativa, dependiendo dicho posicionamiento, siempre, de mi Auto-Percepción; por ejemplo, si yo como individuo percibo que doy más de lo que el Grupo percibe que doy, la diferencia será negativa. Por el contrario, si percibo que doy menos, entonces el Grupo me está reconociendo que doy más de lo que creo que doy: en este caso la diferencia será positiva. 
</p>
<p>
    A esta Brecha de Conciencia le calculamos la desviación estándar, que es el promedio de todas y cada una de las desviaciones, ya que no nos interesa para este caso en particular si el mismo es negativo o positivo, sino en términos absolutos; porque, a pesar de que el valor sea negativo o positivo, si el salto entre la realidad grupal y la creencia individual es poco significativa, lo mismo será Alta Conciencia. Del mismo modo, si el salto entre ambas percepciones es significativo, otra vez: aunque sea negativo o positivo, lo mismo será Baja Conciencia.
</p>
<p>
    Quedarán determinadas así cuatro categorías de Desempeño: 1) Alto: cuando se obtenga Alta Responsabilidad y Alta Conciencia; 2) Medio - Alto: cuando se obtenga Alta responsabilidad y Baja Conciencia; 3) Medio - Bajo, cuando se obtenga Baja Responsabilidad y Alta Conciencia y 4) Bajo, cuando se obtenga Baja Responsabilidad y Baja Conciencia. 
</p>
<p>
    A partir de esta última lectura podemos identificar un Ranking de Conciencia que, como lo muestra la tabla, también advertirse numéricamente de mayor a menor en función de si el valor de la Brecha (Gap de Conciencia) de cada uno está por debajo o por encima de la desviación estándar obtenida. 
</p>
<h3>
    Análisis
</h3>
<p>
    <?= $assessment->report->effectiveness ?>
</p>