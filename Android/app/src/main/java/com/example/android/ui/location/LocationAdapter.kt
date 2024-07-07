package com.example.android.ui.location

import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.BaseAdapter
import android.widget.TextView
import com.example.android.R


class LocationAdapter : BaseAdapter {
    var context: Context
    var locations: MutableList<Location>

    constructor(context: Context, locations: MutableList<Location>) : super() {
        this.context = context
        this.locations = locations
    }

    override fun getCount(): Int {
        return locations.size
    }

    override fun getItem(position: Int): Any {
        return  locations.get(position)
    }

    override fun getItemId(position: Int): Long {
        return locations.get(position).id
    }

    override fun getView(position: Int, convertView: View?, parent: ViewGroup?): View {
        val v: View
        if (convertView==null) {
            var inflater = LayoutInflater.from(this.context)
            v = inflater.inflate(R.layout.location_block,null)
        } else {
            v=convertView
        }
        var current: Location = getItem(position) as Location

        val dd= current.dateDebut.split("-")
        val df= current.dateFin.split("-")
        val fd= formatDate(dd)
        val ff= formatDate(df)
        v.findViewById<TextView>(R.id.locbltitle).text = "Location du "+fd+" au "+ff
        v.findViewById<TextView>(R.id.locbldatefin).text = current.dateFin.split("T")[0]
        v.findViewById<TextView>(R.id.locbldatedeb).text = current.dateDebut.split("T")[0]+" au "
        v.findViewById<TextView>(R.id.locblprice).text = current.price.toString()+"€"
        return v
    }

    private fun formatDate(date: List<String>): String {
        var res =""
        res = if (date[2]== "01"){
             "1er"
        } else { date[2].split("T")[0]
        }
        when (date[1]) {
            "01" -> res += " janvier"
            "02" -> res += " février"
            "03" -> res += " mars"
            "04" -> res += " avril"
            "05" -> res += " mai"
            "06" -> res += " juin"
            "07" -> res += " juillet"
            "08" -> res += " août"
            "09" -> res += " septembre"
            "10" -> res += " octobre"
            "11" -> res += " novembre"
            "12" -> res += " décembre"
        }
        res += " "+date[0]
        return res
    }
}