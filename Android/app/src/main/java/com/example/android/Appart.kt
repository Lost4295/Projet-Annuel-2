package com.example.android

class Appart {
    var id: Long
    var description:String
    var shortDesc:String
    var price:Int
    var address: String
    var city: String
    var postalCode: String
    var country:String
    var note: Int
    var state: String
    var images:MutableList<String>
    var bailleur: String
    var titre:String
    var nbchambers: Int
    var nbbathrooms: Int
    var nbBeds: Int
    var appartPluses: MutableList<String>
    var createdAt: String
    var updatedAt: String
    var surface: Int
    var locations: MutableList<String>


    constructor(
        id: Long,
        description: String,
        shortDesc: String,
        price: Int,
        address: String,
        city: String,
        postalCode: String,
        country: String,
        note: Int,
        state: String,
        images: MutableList<String>,
        bailleur: String,
        titre: String,
        nbchambers: Int,
        nbbathrooms: Int,
        nbBeds: Int,
        appartPluses: MutableList<String>,
        createdAt: String,
        updatedAt: String,
        surface: Int,
        locations: MutableList<String>
    ) {
        this.id = id
        this.description = description
        this.shortDesc = shortDesc
        this.price = price
        this.address = address
        this.city = city
        this.postalCode = postalCode
        this.country = country
        this.note = note
        this.state = state
        this.images = images
        this.bailleur = bailleur
        this.titre = titre
        this.nbchambers = nbchambers
        this.nbbathrooms = nbbathrooms
        this.nbBeds = nbBeds
        this.appartPluses = appartPluses
        this.createdAt = createdAt
        this.updatedAt = updatedAt
        this.surface = surface
        this.locations = locations
    }
}