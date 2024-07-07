package com.example.android.ui.services

import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.BaseAdapter
import android.widget.ImageView
import android.widget.TextView
import com.example.android.R
import com.squareup.picasso.Picasso


class ServiceAdapter : BaseAdapter {
    var context: Context
    var services: MutableList<Service>

    constructor(context: Context, services: MutableList<Service>) : super() {
        this.context = context
        this.services = services
    }

    override fun getCount(): Int {
        return services.size
    }

    override fun getItem(position: Int): Any {
        return  services.get(position)
    }

    override fun getItemId(position: Int): Long {
        return services.get(position).id
    }

    override fun getView(position: Int, convertView: View?, parent: ViewGroup?): View {
        var v: View
        if (convertView==null) {
            var inflater = LayoutInflater.from(this.context)
            v = inflater.inflate(R.layout.service_block,null)
        } else {
            v=convertView
        }
        var current: Service = getItem(position) as Service
        Picasso.get().load("https://pariscaretakerservices.fr/images/services/"+current.image).resize(100,100).into(
                v.findViewById<ImageView>(R.id.servblid))
        v.findViewById<TextView>(R.id.servbltext).text = current.text
        v.findViewById<TextView>(R.id.servbltitle).text = current.title

        return v
    }

}