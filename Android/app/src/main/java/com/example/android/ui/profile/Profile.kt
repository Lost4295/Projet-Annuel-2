package com.example.android.ui.profile

class Profile {
    var name: String
    var firstname: String
    var email: String
    var phone: String
    var birthdate: String
    var abonnement: String

    constructor(name: String, firstname:String, email: String, phone: String, birthdate: String, abonnement: String) {
        this.name = name
        this.firstname = firstname
        this.email = email
        this.phone = phone
        this.birthdate = birthdate
        this.abonnement = abonnement
    }
}