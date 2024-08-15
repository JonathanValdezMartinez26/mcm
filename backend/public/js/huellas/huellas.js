const LectorHuellas = (() => {
    class LectorHuellas {
        constructor({ notificacion = () => {}, procesador = () => {} } = {}) {
            this.FP = new Fingerprint.WebApi()

            this.FP.onDeviceConnected = this.lectorConectado
            this.FP.onDeviceDisconnected = this.lectorDesconectado
            this.FP.onCommunicationFailed = this.errorComunicacion
            this.FP.onSamplesAcquisitionStarted = () => this.notificacion(this.estatus.lecturaI)
            this.FP.onSamplesAcquired = this.lectura
            this.FP.onSamplesAcquisitionStopped = () => this.notificacion(this.estatus.lecturaF)

            this.lectorActivo = false
            this.lector = null
            this.formatos = {
                imagen: Fingerprint.SampleFormat.PngImage,
                binario: Fingerprint.SampleFormat.Raw,
                intermedio: Fingerprint.SampleFormat.Intermediate
            }
            this.formato = this.formatos.intermedio
            this.notificacion = notificacion
            this.procesador = procesador

            this.errores = {
                sinLector: "No se ha detectado ningún lector de huella.",
                sinHuella: "No se ha capturado ninguna huella.",
                multipleLectores: "Se han detectado múltiples lectores de huella.",
                comunicacion: "Error de comunicación con el lector de huella."
            }

            this.estatus = {
                conectado: "Lector de huella conectado.",
                desconectado: "Lector de huella desconectado.",
                lecturaI:
                    "Coloque firmemente el dedo sobre el lector y no lo retire hasta que la luz roja se apague.",
                lecturaF: "El lector de huella ha terminado de capturar la muestra.",
                lecturaOK: "Muestra capturada correctamente.",
                reconectado: "Lector de huella reconectado, puede continuar."
            }
        }

        lectorConectado = (lector) => {
            if (this.lectorActivo) return
            if (this.lector === lector.deviceUid) {
                this.lectorActivo = true
                return this.notificacion(this.estatus.reconectado)
            }

            if (!this.lectorActivo && !this.lector) {
                this.setLector(lector)
                this.lectorActivo = true
            }
            return this
        }

        lectorDesconectado = () => {
            this.notificacion(this.estatus.desconectado, true)
            this.lectorActivo = false
            return this
        }

        lectura = (lectura) => {
            const datos = JSON.parse(lectura.samples)
            this.procesador(datos[0])
            return this
        }

        iniciarCaptura = () => {
            if (!this.lector) return this.errorSinLector()
            this.notificacion(this.estatus.lecturaI)
            return this.FP.startAcquisition(this.formato, this.lector)
        }

        detenerCaptura = () => {
            if (!this.lector) return this.errorSinLector()
            return this.FP.stopAcquisition(this.lector)
        }

        lectoresConectados = () => this.getLectores().length

        getLectores = () => this.FP.enumerateDevices()

        getEstatusLector = () => this.lectorActivo

        getURL = (datos) => Fingerprint.b64UrlTo64(datos)

        setLector = (lector) => {
            this.notificacion(this.estatus.conectado)
            this.lector = lector
            this.lectorActivo = true
            return this
        }

        setFormato = (formato) => {
            if (!this.formatos[formato]) return this
            this.formato = this.formatos[formato]
            return this
        }

        setProcesador = (procesador) => {
            this.procesador = procesador
            return this
        }

        errorSinLector = () => Promise.reject(new Error(this.errores.sinLector))

        errorMultilpleLector = () => Promise.reject(new Error(this.errores.multipleLectores))

        errorComunicacion = () => Promise.reject(new Error(this.errores.comunicacion))
    }
    return LectorHuellas
})()

class Mano {
    constructor(mano, lector, contenedor = null) {
        this.mano = mano
        this.datosCliente = null
        this.dedos = {}
        this.lector = lector
        this.contenedor = contenedor
        this.autorizado = false
        if (this.contenedor) {
            this.contenedor.querySelectorAll(".huella-contenedor").forEach((dedo) => {
                this.dedos[dedo.id] = new Dedo(this.lector, this.contenedor, this.mano, dedo.id)
            })
        }
    }

    getMano() {
        const mano = {}
        mano[this.mano] = {}
        Object.keys(this.dedos).forEach((dedo) =>
            Object.assign(mano[this.mano], this.dedos[dedo].getDedo())
        )
        return mano
    }

    limpiarMano() {
        Object.keys(this.dedos).forEach((dedo) => this.dedos[dedo].limpiar())
    }

    manoLista() {
        return Object.keys(this.dedos).every((dedo) => this.dedos[dedo].listo())
    }

    capturaMuestra(muestra, dedo, elemento) {
        if (!this.dedos[dedo]) return
        this.dedos[dedo].captura(muestra, dedo, elemento)
    }

    modoValidacion() {
        Object.keys(this.dedos).forEach((dedo) => this.dedos[dedo].modoValidacion())
    }

    modoCaptura() {
        Object.keys(this.dedos).forEach((dedo) => this.dedos[dedo].modoCaptura())
    }

    modoAutorizacion() {
        this.autorizado = false
        Object.keys(this.dedos).forEach((dedo) => this.dedos[dedo].modoAutorizacion())
    }
}

class Dedo {
    constructor(lector, contenedor, mano, dedo) {
        this.lector = lector
        this.contenedor = contenedor
        this.muestrasRequeridas = 5
        this.erroresValidacion = 0
        this.mano = mano
        this.dedo = dedo
        this.imagen = null
        this.etiqueta = null
        this.selector = null
        this.muestras = []
        this.modoActivo = "captura"

        this.setImagen()
        this.setEtiqueta()
        this.setSelector()
        this.setBoton()
    }

    setImagen() {
        this.imagen = this.contenedor.querySelector(`#imagen_${this.dedo}`)
        if (!this.imagen) return
        this.configurarImagen()
    }

    configurarImagen() {
        this.imagen.addEventListener("click", this.captura.bind(this))
        this.punterosImagen()
    }

    punterosImagen() {
        this.imagen.addEventListener("mouseover", () => {
            if (this.listo()) return (this.imagen.style.cursor = "not-allowed")
            this.imagen.style.cursor = "pointer"
        })
        this.imagen.addEventListener("mouseout", () => (this.imagen.style.cursor = "default"))
    }

    setEtiqueta() {
        this.etiqueta = document.querySelector(`#etiqueta_${this.dedo}`)
        if (this.etiqueta) this.setTextoEtiqueta()
    }

    setSelector() {
        this.selector = this.contenedor.querySelector(`#selector_${this.dedo}`)
        if (!this.selector) return
        this.dedoSeleccionado()
        this.selector.addEventListener("change", this.dedoSeleccionado.bind(this))
    }

    setTextoEtiqueta() {
        if (this.etiqueta)
            this.etiqueta.innerHTML = `${this.muestras.length} muestras de ${this.muestrasRequeridas}`
    }

    setBoton() {
        this.boton = this.contenedor.querySelector(`#boton_${this.dedo}`)
        if (this.boton) this.boton.addEventListener("click", this.limpiar.bind(this))
    }

    setColorImagen(color) {
        this.imagen.querySelector("#fondoHuella").style.fill = color
    }

    dedoSeleccionado() {
        this.contenedor.querySelectorAll("select").forEach((dedo) => {
            if (this.selector.id !== dedo.id) {
                dedo.querySelectorAll("option").forEach((opcion) => {
                    opcion.style.display = opcion.value === this.selector.value ? "none" : "block"
                })
            }
        })
    }

    addMuestra(muestra) {
        this.muestras.push(muestra)
    }

    getMuestras() {
        return this.muestras
    }

    getDedo() {
        const dedo = {}
        const nombreDedo = this.getNombreDedo()

        dedo[nombreDedo] = this.muestras
        return dedo
    }

    getNombreDedo() {
        return this.selector.options[this.selector.selectedIndex].text
            .toLowerCase()
            .replace("í", "i")
            .replace("ñ", "n")
    }

    listo() {
        return this.muestras.length === this.muestrasRequeridas
    }

    limpiar() {
        this.muestras = []
        this.erroresValidacion = 0
        this.setTextoEtiqueta()
        this.actualizaProgreso()
        this.contenedor.dispatchEvent(
            new CustomEvent("muestraObtenida", {
                detail: {
                    modo: this.modoActivo,
                    muestrasOK: this.listo()
                }
            })
        )
    }

    actualizaProgreso(valor = null) {
        const progreso = valor || (this.muestras.length / this.muestrasRequeridas) * 100
        this.selector.disabled = progreso !== 0
        this.imagen.querySelectorAll("stop").forEach((stop, i) => {
            if (i == 1 || i == 2) stop.setAttribute("offset", `${progreso}%`)
        })
    }

    actualizaConteoErrores(valor = null) {
        this.erroresValidacion = valor || this.erroresValidacion + 1
    }

    eventoActualizacion() {
        this.contenedor.dispatchEvent(
            new CustomEvent("actualizaHuella", {
                detail: {
                    muestras: this.muestras,
                    dedo: this.getNombreDedo(),
                    mano: this.mano,
                    colorImagen: this.setColorImagen.bind(this),
                    boton: this.boton,
                    erroresValidacion: this.erroresValidacion,
                    conteoErrores: this.actualizaConteoErrores.bind(this),
                    mensajeLector: this.lector.notificacion.bind(this.lector),
                    valida: this.modoValidacion.bind(this),
                    limpiar: this.limpiar.bind(this)
                }
            })
        )
    }

    modoAutorizacion() {
        this.modoActivo = "autorizacion"
        this.etiqueta.style.display = "none"
        this.muestras = []
        this.erroresValidacion = 0
        this.actualizaProgreso(0)
        this.selector.style.display = "none"
        this.setColorImagen(null)

        const imagen = this.imagen.cloneNode(true)
        imagen.addEventListener("click", this.validacion.bind(this))

        const boton = this.boton.cloneNode(true)
        boton.style.display = "none"

        this.imagen.replaceWith(imagen)
        this.boton.replaceWith(boton)
        this.imagen = imagen
        this.boton = boton
        this.validacion()
        this.punterosImagen()
    }

    modoValidacion() {
        this.modoActivo = "validacion"
        this.etiqueta.style.display = "none"
        this.muestras = []
        this.erroresValidacion = 0
        this.actualizaProgreso(0)
        this.selector.disabled = true
        this.selector.style.display = "block"
        this.setColorImagen(null)

        const imagen = this.imagen.cloneNode(true)
        const boton = this.boton.cloneNode(true)

        boton.innerHTML = "Validar"
        boton.style.display = "block"
        boton.addEventListener("click", this.validacion.bind(this))

        this.imagen.replaceWith(imagen)
        this.boton.replaceWith(boton)
        this.imagen = imagen
        this.boton = boton
        this.punterosImagen()
    }

    modoCaptura() {
        this.modoActivo = "captura"
        this.etiqueta.style.display = "block"
        this.muestras = []
        this.erroresValidacion = 0
        this.actualizaProgreso(0)
        this.selector.style.display = "block"
        this.setColorImagen(null)

        const imagen = this.imagen.cloneNode(true)
        const boton = this.boton.cloneNode(true)

        boton.innerHTML = "Limpiar"
        boton.style.display = "block"
        boton.addEventListener("click", this.limpiar.bind(this))

        this.imagen.replaceWith(imagen)
        this.boton.replaceWith(boton)
        this.imagen = imagen
        this.boton = boton
        this.configurarImagen()
    }

    modoActualizacion() {
        this.modoActivo = "actualizacion"
        this.etiqueta.style.display = "block"
        this.muestras = []
        this.erroresValidacion = 0
        this.actualizaProgreso(0)
        this.setTextoEtiqueta()
        this.selector.style.display = "block"
        this.setColorImagen(null)

        const imagen = this.imagen.cloneNode(true)
        const boton = this.boton.cloneNode(true)

        boton.innerHTML = "Limpiar"
        boton.style.display = "block"
        boton.addEventListener("click", this.limpiar.bind(this))

        this.imagen.replaceWith(imagen)
        this.boton.replaceWith(boton)
        this.imagen = imagen
        this.boton = boton
        this.configurarImagen()
    }

    captura() {
        if (this.listo()) return
        this.lector
            .getLectores()
            .then((lectores) => {
                this.compruebaLector(lectores)
            })
            .then(() => {
                if (!this.lector.getEstatusLector()) return

                this.lector.setProcesador((sample) => {
                    this.addMuestra(sample.Data)
                    this.setTextoEtiqueta()
                    this.actualizaProgreso()
                    this.lector.notificacion(this.lector.estatus.lecturaOK)
                    this.lector.detenerCaptura()
                    this.contenedor.dispatchEvent(
                        new CustomEvent("muestraObtenida", {
                            detail: {
                                modo: this.modoActivo,
                                muestrasOK: this.listo(),
                                boton: this.boton,
                                evento: this.eventoActualizacion.bind(this)
                            }
                        })
                    )
                })

                this.lector.iniciarCaptura()
            })
            .catch((error) => this.lector.notificacion(error.message, true))
    }

    validacion() {
        if (this.modoActivo == "validacion" && this.erroresValidacion >= 2) {
            this.lector.notificacion(
                "Se ha alcanzado el número máximo de intentos, vuelva a capturar la información.",
                true
            )
            this.modoActualizacion()
            return
        }
        this.imagen.querySelector("#fondoHuella").style.fill = null
        this.lector
            .getLectores()
            .then((lectores) => {
                this.compruebaLector(lectores)
            })
            .then(() => {
                if (!this.lector.getEstatusLector()) return

                this.lector.setProcesador((sample) => {
                    this.addMuestra(sample.Data)
                    this.lector.notificacion(this.lector.estatus.lecturaOK)
                    this.lector.detenerCaptura()
                    this.contenedor.dispatchEvent(
                        new CustomEvent("validaHuella", {
                            detail: {
                                muestra: sample.Data,
                                dedo: this.getNombreDedo() + "_" + this.mano.charAt(0),
                                colorImagen: this.setColorImagen.bind(this),
                                boton: this.boton,
                                erroresValidacion: this.erroresValidacion,
                                conteoErrores: this.actualizaConteoErrores.bind(this),
                                mensajeLector: this.lector.notificacion.bind(this.lector),
                                actualliza: this.modoActualizacion.bind(this)
                            }
                        })
                    )
                })

                this.lector.iniciarCaptura()
            })
    }

    compruebaLector(lectores) {
        if (!lectores.length)
            return this.lector
                .errorSinLector()
                .catch((error) => this.lector.notificacion(error.message, true))

        if (lectores.length === 1) return this.lector.setLector(lectores[0])

        this.lector
            .errorMiltilpleLector()
            .catch((error) => this.lector.notificacion(error.message, true))

        lectores.forEach((lector) => console.log(lector))
    }
}
