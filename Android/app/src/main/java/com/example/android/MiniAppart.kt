package com.example.android

class MiniAppart {
    var id: Long
    var shortDesc:String
    var price:Int
    var address: String
    var city: String
    var country:String
    var images:String
    var bailleur: String
    var titre:String

    constructor(
        id: Long,
        shortDesc: String,
        price: Int,
        address: String,
        city: String,
        country: String,
        images: String,
        bailleur: String,
        titre: String
    ) {
        this.id = id
        this.shortDesc = shortDesc
        this.price = price
        this.address = address
        this.city = city
        this.country = country
        this.images = images
        this.bailleur = bailleur
        this.titre = titre
    }
}