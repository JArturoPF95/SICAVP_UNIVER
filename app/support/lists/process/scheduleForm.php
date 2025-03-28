<?php

$formInsert = '
                        <div class="form-floating mb-3">
                            <input type="text" name="payroll" class="form-control form-control-sm" id="floatingInput" placeholder="Guardias Fin Ciclo Ventas" required>
                            <label for="floatingInput">Jornada</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" name="schedule_name" class="form-control form-control-sm" id="floatingInput" placeholder="Lun a Vie 14:00 a 15:00, Sab 08:00 a 10:00" required>
                            <label for="floatingInput">Horario</label>
                        </div>
                        <!-- Lunes -->
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="disabledTextInput" class="form-label">Lunes</label>
                                <input hidden type="text" name="schedule[0][day]" id="disabledTextInput" class="form-control form-control-sm" value="1">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Entrada Previa</label>
                                <input type="text" name="schedule[0][minIn]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Hora Entrada</label>
                                <input type="time" name="schedule[0][start_time]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Retardo</label>
                                <input type="text" name="schedule[0][delay]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Falta</label>
                                <input type="text" name="schedule[0][abscence]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Salida Previa</label>
                                <input type="text" name="schedule[0][prevOut]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Hora Salida</label>
                                <input type="time" name="schedule[0][end_time]" class="form-control form-control-sm">
                            </div>
                        </div>

                        <!-- Martes -->
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="disabledTextInput" class="form-label">Martes</label>
                                <input hidden type="text" name="schedule[1][day]" id="disabledTextInput" class="form-control form-control-sm" value="2">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Entrada Previa</label>
                                <input type="text" name="schedule[1][minIn]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Hora Entrada</label>
                                <input type="time" name="schedule[1][start_time]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Retardo</label>
                                <input type="text" name="schedule[1][delay]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Falta</label>
                                <input type="text" name="schedule[1][abscence]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Salida Previa</label>
                                <input type="text" name="schedule[1][prevOut]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Hora Salida</label>
                                <input type="time" name="schedule[1][end_time]" class="form-control form-control-sm">
                            </div>
                        </div>

                        <!-- Miércoles -->
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="disabledTextInput" class="form-label">Miércoles</label>
                                <input hidden type="text" name="schedule[2][day]" id="disabledTextInput" class="form-control form-control-sm" value="3">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Entrada Previa</label>
                                <input type="text" name="schedule[2][minIn]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Hora Entrada</label>
                                <input type="time" name="schedule[2][start_time]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Retardo</label>
                                <input type="text" name="schedule[2][delay]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Falta</label>
                                <input type="text" name="schedule[2][abscence]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Salida Previa</label>
                                <input type="text" name="schedule[2][prevOut]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Hora Salida</label>
                                <input type="time" name="schedule[2][end_time]" class="form-control form-control-sm">
                            </div>
                        </div>

                        <!-- Jueves -->
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="disabledTextInput" class="form-label">Jueves</label>
                                <input hidden type="text" name="schedule[3][day]" id="disabledTextInput" class="form-control form-control-sm" value="4">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Entrada Previa</label>
                                <input type="text" name="schedule[3][minIn]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Hora Entrada</label>
                                <input type="time" name="schedule[3][start_time]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Retardo</label>
                                <input type="text" name="schedule[3][delay]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Falta</label>
                                <input type="text" name="schedule[3][abscence]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Salida Previa</label>
                                <input type="text" name="schedule[3][prevOut]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Hora Salida</label>
                                <input type="time" name="schedule[3][end_time]" class="form-control form-control-sm">
                            </div>
                        </div>

                        <!-- Viernes -->
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="disabledTextInput" class="form-label">Viernes</label>
                                <input hidden type="text" name="schedule[4][day]" id="disabledTextInput" class="form-control form-control-sm" value="5">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Entrada Previa</label>
                                <input type="text" name="schedule[4][minIn]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Hora Entrada</label>
                                <input type="time" name="schedule[4][start_time]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Retardo</label>
                                <input type="text" name="schedule[4][delay]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Falta</label>
                                <input type="text" name="schedule[4][abscence]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Salida Previa</label>
                                <input type="text" name="schedule[4][prevOut]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Hora Salida</label>
                                <input type="time" name="schedule[4][end_time]" class="form-control form-control-sm">
                            </div>
                        </div>

                        <!-- Sábado -->
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="disabledTextInput" class="form-label">Sábado</label>
                                <input hidden type="text" name="schedule[5][day]" id="disabledTextInput" class="form-control form-control-sm" value="6">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Entrada Previa</label>
                                <input type="text" name="schedule[5][minIn]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Hora Entrada</label>
                                <input type="time" name="schedule[5][start_time]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Retardo</label>
                                <input type="text" name="schedule[5][delay]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Falta</label>
                                <input type="text" name="schedule[5][abscence]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Salida Previa</label>
                                <input type="text" name="schedule[5][prevOut]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Hora Salida</label>
                                <input type="time" name="schedule[5][end_time]" class="form-control form-control-sm">
                            </div>
                        </div>

                        <!-- Domingo -->
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="disabledTextInput" class="form-label">Domingo</label>
                                <input hidden type="text" name="schedule[6][day]" id="disabledTextInput" class="form-control form-control-sm" value="7">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Entrada Previa</label>
                                <input type="text" name="schedule[6][minIn]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Hora Entrada</label>
                                <input type="time" name="schedule[6][start_time]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Retardo</label>
                                <input type="text" name="schedule[6][delay]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Falta</label>
                                <input type="text" name="schedule[6][abscence]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Salida Previa</label>
                                <input type="text" name="schedule[6][prevOut]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="inputEmail4" class="form-label">Hora Salida</label>
                                <input type="time" name="schedule[6][end_time]" class="form-control form-control-sm">
                            </div>
                        </div>
';