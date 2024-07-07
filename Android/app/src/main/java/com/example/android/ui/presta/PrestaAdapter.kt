package com.example.android.ui.presta

import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.BaseAdapter
import android.widget.ImageView
import android.widget.TextView
import com.example.android.R
import com.squareup.picasso.Picasso

class PrestaAdapter : BaseAdapter {
    var context: Context
    var prestations: MutableList<Presta>

    constructor(context: Context, prestations: MutableList<Presta>) : super() {
        this.context = context
        this.prestations = prestations
    }

    override fun getCount(): Int {
        return prestations.size
    }

    override fun getItem(position: Int): Any {
        return prestations.get(position)
    }

    override fun getItemId(position: Int): Long {

    return prestations.get(position).id
    }

    override fun getView(position: Int, convertView: View?, parent: ViewGroup?): View {
        val v: View
        if (convertView==null) {
            var inflater = LayoutInflater.from(this.context)
            v = inflater.inflate(R.layout.presta_block,null)
        } else {
            v=convertView
        }
        var current: Presta = getItem(position) as Presta

        Picasso.get().load("https://pariscaretakerservices.fr/images/presta/"+current.image).resize(100,100).into(v.findViewById<ImageView>(R.id.presblimg))
        v.findViewById<TextView>(R.id.presblname).text = current.societyName

        return v
    }

}