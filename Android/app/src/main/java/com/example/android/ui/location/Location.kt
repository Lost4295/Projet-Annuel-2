package com.example.android.ui.location

import com.example.android.ui.services.Service

class Location {
    var id: Long
    var dateDebut: String
    var dateFin: String
    var appartement: String
    var locataire: String
    var adults: Int
    var kids: Int
    var babies: Int
    var price: Int
    var Services: MutableList<Service>

    constructor(
        id: Long,
        dateDebut: String,
        dateFin: String,
        appartement: String,
        locataire: String,
        adults: Int,
        kids: Int,
        babies: Int,
        price: Int,
        Services: MutableList<Service>
    ) {
        this.id = id
        this.dateDebut = dateDebut
        this.dateFin = dateFin
        this.appartement = appartement
        this.locataire = locataire
        this.adults = adults
        this.kids = kids
        this.babies = babies
        this.price = price
        this.Services = Services
    }
}