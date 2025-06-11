<!-- Lisa raamatu modal -->
<div class="modal fade" id="lisaRaamatModal" tabindex="-1" aria-labelledby="lisaRaamatModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lisaRaamatModalLabel">Lisa uus raamat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="admin_actions.php?action=add_book" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="lisaPealkiri" class="form-label">Pealkiri</label>
                        <input type="text" class="form-control" id="lisaPealkiri" name="pealkiri" required>
                    </div>
                    <div class="mb-3">
                        <label for="lisaAutor" class="form-label">Autor</label>
                        <select class="form-select" id="lisaAutor" name="autor_id" required>
                            <?php
                            $conn = connectDB();
                            $sql = "SELECT id, eesnimi, perekonnanimi FROM autorid ORDER BY perekonnanimi, eesnimi";
                            $result = $conn->query($sql);
                            
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<option value="' . $row['id'] . '">' . 
                                         htmlspecialchars($row['perekonnanimi'] . ', ' . $row['eesnimi']) . '</option>';
                                }
                            }
                            $conn->close();
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="lisaIsbn" class="form-label">ISBN</label>
                        <input type="text" class="form-control" id="lisaIsbn" name="isbn" required
                               pattern="^(?:ISBN(?:-1[03])?:? )?(?=[0-9X]{10}$|(?=(?:[0-9]+[- ]){3})[- 0-9X]{13}$|97[89][0-9]{10}$|(?=(?:[0-9]+[- ]){4})[- 0-9]{17}$)(?:97[89][- ]?)?[0-9]{1,5}[- ]?[0-9]+[- ]?[0-9]+[- ]?[0-9X]$"
                               title="Sisesta korrektne ISBN number">
                    </div>
                    <div class="mb-3">
                        <label for="lisaAasta" class="form-label">Väljaande aasta</label>
                        <input type="number" class="form-control" id="lisaAasta" name="aasta" 
                               min="1000" max="<?= date('Y') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="lisaEksemplarid" class="form-label">Eksemplaride arv</label>
                        <input type="number" class="form-control" id="lisaEksemplarid" name="eksemplarid" min="1" value="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="lisaKirjeldus" class="form-label">Kirjeldus</label>
                        <textarea class="form-control" id="lisaKirjeldus" name="kirjeldus" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tühista</button>
                    <button type="submit" class="btn btn-primary">Lisa raamat</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Muuda raamatu modal -->
<div class="modal fade" id="muudaRaamatModal" tabindex="-1" aria-labelledby="muudaRaamatModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="muudaRaamatModalLabel">Muuda raamatu andmeid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="admin_actions.php?action=update_book" method="post">
                <input type="hidden" name="id" id="muudaRaamatId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="muudaPealkiri" class="form-label">Pealkiri</label>
                        <input type="text" class="form-control" id="muudaPealkiri" name="pealkiri" required>
                    </div>
                    <div class="mb-3">
                        <label for="muudaIsbn" class="form-label">ISBN</label>
                        <input type="text" class="form-control" id="muudaIsbn" name="isbn" required>
                    </div>
                    <div class="mb-3">
                        <label for="muudaEksemplarid" class="form-label">Eksemplaride arv</label>
                        <input type="number" class="form-control" id="muudaEksemplarid" name="eksemplarid" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tühista</button>
                    <button type="submit" class="btn btn-primary">Salvesta muudatused</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Lisa autori modal -->
<div class="modal fade" id="lisaAutorModal" tabindex="-1" aria-labelledby="lisaAutorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lisaAutorModalLabel">Lisa uus autor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="admin_actions.php?action=add_author" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="lisaAutorEesnimi" class="form-label">Eesnimi</label>
                        <input type="text" class="form-control" id="lisaAutorEesnimi" name="eesnimi" required>
                    </div>
                    <div class="mb-3">
                        <label for="lisaAutorPerekonnanimi" class="form-label">Perekonnanimi</label>
                        <input type="text" class="form-control" id="lisaAutorPerekonnanimi" name="perekonnanimi" required>
                    </div>
                    <div class="mb-3">
                        <label for="lisaAutorSynniaeg" class="form-label">Sünniaeg</label>
                        <input type="date" class="form-control" id="lisaAutorSynniaeg" name="synniaeg">
                    </div>
                    <div class="mb-3">
                        <label for="lisaAutorRiik" class="form-label">Riik</label>
                        <input type="text" class="form-control" id="lisaAutorRiik" name="riik">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tühista</button>
                    <button type="submit" class="btn btn-primary">Lisa autor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Muuda autori modal -->
<div class="modal fade" id="muudaAutorModal" tabindex="-1" aria-labelledby="muudaAutorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="muudaAutorModalLabel">Muuda autori andmeid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="admin_actions.php?action=update_author" method="post">
                <input type="hidden" name="id" id="muudaAutorId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="muudaAutorEesnimi" class="form-label">Eesnimi</label>
                        <input type="text" class="form-control" id="muudaAutorEesnimi" name="eesnimi" required>
                    </div>
                    <div class="mb-3">
                        <label for="muudaAutorPerekonnanimi" class="form-label">Perekonnanimi</label>
                        <input type="text" class="form-control" id="muudaAutorPerekonnanimi" name="perekonnanimi" required>
                    </div>
                    <div class="mb-3">
                        <label for="muudaAutorSynniaeg" class="form-label">Sünniaeg</label>
                        <input type="date" class="form-control" id="muudaAutorSynniaeg" name="synniaeg">
                    </div>
                    <div class="mb-3">
                        <label for="muudaAutorRiik" class="form-label">Riik</label>
                        <input type="text" class="form-control" id="muudaAutorRiik" name="riik">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tühista</button>
                    <button type="submit" class="btn btn-primary">Salvesta muudatused</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Muuda kasutaja modal -->
<div class="modal fade" id="muudaKasutajaModal" tabindex="-1" aria-labelledby="muudaKasutajaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="muudaKasutajaModalLabel">Muuda kasutaja andmeid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="admin_actions.php?action=update_user" method="post">
                <input type="hidden" name="id" id="muudaKasutajaId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="muudaKasutajaEesnimi" class="form-label">Eesnimi</label>
                        <input type="text" class="form-control" id="muudaKasutajaEesnimi" name="eesnimi" required>
                    </div>
                    <div class="mb-3">
                        <label for="muudaKasutajaPerekonnanimi" class="form-label">Perekonnanimi</label>
                        <input type="text" class="form-control" id="muudaKasutajaPerekonnanimi" name="perekonnanimi" required>
                    </div>
                    <div class="mb-3">
                        <label for="muudaKasutajaIsikukood" class="form-label">Isikukood</label>
                        <input type="text" class="form-control" id="muudaKasutajaIsikukood" name="isikukood" required
                               pattern="[0-9]{11}" title="Isikukood peab koosnema 11 numbrist">
                    </div>
                    <div class="mb-3">
                        <label for="muudaKasutajaEmail" class="form-label">E-posti aadress</label>
                        <input type="email" class="form-control" id="muudaKasutajaEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="muudaKasutajaRoll" class="form-label">Roll</label>
                        <select class="form-select" id="muudaKasutajaRoll" name="roll" required>
                            <option value="külastaja">Külastaja</option>
                            <option value="admin">Administraator</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tühista</button>
                    <button type="submit" class="btn btn-primary">Salvesta muudatused</button>
                </div>
            </form>
        </div>
    </div>
</div>