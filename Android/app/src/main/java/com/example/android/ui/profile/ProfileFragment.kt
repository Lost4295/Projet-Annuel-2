package com.example.android.ui.profile

import android.os.Bundle
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.LinearLayout
import android.widget.ListView
import android.widget.ProgressBar
import android.widget.TextView
import androidx.fragment.app.Fragment
import androidx.navigation.findNavController
import com.android.volley.Request
import com.android.volley.Response
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import com.example.android.R
import com.example.android.databinding.FragmentProfileBinding
import com.example.android.ui.documents.Document
import com.example.android.ui.documents.DocumentAdapter
import com.example.android.ui.location.Location
import com.example.android.ui.location.LocationAdapter
import com.example.android.ui.services.Service
import com.google.android.material.tabs.TabLayout

class ProfileFragment : Fragment() {

    private var _binding: FragmentProfileBinding? = null

    // This property is only valid between onCreateView and
    // onDestroyView.
    private val binding get() = _binding!!

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = FragmentProfileBinding.inflate(inflater, container, false)
        val root: View = binding.root
        var tabs = root.findViewById<TabLayout>(R.id.tabs)
        val data1 = root.findViewById<LinearLayout>(R.id.data1)
        val data2 = root.findViewById<LinearLayout>(R.id.data2)
        val data3 = root.findViewById<LinearLayout>(R.id.data3)
        tabs.addOnTabSelectedListener(object : TabLayout.OnTabSelectedListener {
            override fun onTabSelected(tab: TabLayout.Tab?) {
                var queue = Volley.newRequestQueue(requireContext())
                var shp = requireContext().getSharedPreferences("user", 0)
                val token = shp.getString("usrtoken", null)
                when (tab?.position) {
                    0 -> {
                        data1.visibility = View.VISIBLE
                        data2.visibility = View.GONE
                        data3.visibility = View.GONE
                        val name = shp.getString("namer", null)
                        val firstname = shp.getString("rname", null)
                        val email = shp.getString("name", null)
                        val phone = shp.getString("phone", null)
                        val birthdate = shp.getString("birthdate", null)
                        val abonnement = shp.getString("abonnement", null)
                        val tvname: TextView = root.findViewById(R.id.profblnom)
                        val tvfirstname: TextView = root.findViewById(R.id.profblprenom)
                        val tvemail: TextView = root.findViewById(R.id.profblemail)
                        val tvphone: TextView = root.findViewById(R.id.profblphone)
                        val tvbirthdate: TextView = root.findViewById(R.id.profblbirth)
                        val tvabonnement: TextView = root.findViewById(R.id.profblabo)
                        tvname.text = name
                        tvfirstname.text = firstname
                        tvemail.text = email
                        tvphone.text = phone
                        tvbirthdate.text = birthdate
                        tvabonnement.text = abonnement
                    }
                    1 -> {
                        data1.visibility = View.GONE
                        data2.visibility = View.VISIBLE
                        data3.visibility = View.GONE
                        val name = shp.getString("name", null)
                        val id = shp.getString("id", null)
                        val spinner = root.findViewById<ProgressBar>(R.id.progressBar2);
                        val textView: TextView = root.findViewById(R.id.tv_loadingloc)
                        val locslv = root.findViewById<ListView>(R.id.lv_location)
                        spinner.visibility = View.VISIBLE
                        textView.visibility = View.VISIBLE
                        locslv.visibility = View.GONE

                        if (name != null) {
                            var locs: JsonObjectRequest = object : JsonObjectRequest(
                                Request.Method.GET,
                                "https://api.pariscaretakerservices.fr/usrlocs",
                                null,
                                Response.Listener{ content ->
                                    var listloc: MutableList<Location> = mutableListOf<Location>()
                                    Log.d("LocationFragment", "onViewCreated: $content")
                                    try {
                                        val cur_jso =content.getJSONArray("data")
                                        Log.d("LocationFragment", "onViewCreated: $cur_jso")
                                        for ( i in 0..cur_jso.length()-1){
                                            val loc = cur_jso.getJSONObject(i)
                                            var services : MutableList<Service> = mutableListOf<Service>()
                                            val serv = loc.getJSONArray("services")
                                            for (j in 0..<serv.length()){
                                                services.add(
                                                    Service(0,serv.getJSONObject(j).getString("titre"),
                                                    serv.getJSONObject(j).getString("description"),
                                                    serv.getJSONObject(j).getString("image"),
                                                )
                                                )
                                            }
                                            val location = Location(
                                                loc.getLong("id"),
                                                loc.getString("dateDebut"),
                                                loc.getString("dateFin"),
                                                loc.getString("appartement"),
                                                loc.getString("locataire"),
                                                loc.getInt("adults"),
                                                loc.getInt("kids"),
                                                loc.getInt("babies"),
                                                loc.getInt("price"),
                                                services
                                            )
                                            listloc.add(location)
                                            Log.i("LocationFragment", "onViewCreated: $location")
                                        }
                                        val adapter = LocationAdapter(requireContext(), listloc)
                                        locslv.adapter = adapter
                                        spinner.visibility = View.GONE
                                        textView.visibility = View.GONE
                                        locslv.visibility = View.VISIBLE

                                    } catch (e: Exception) {
                                        Log.e("LocationFragment", "onViewCreated: ", e)
                                    }
                                },
                                Response.ErrorListener { error ->
                                    Log.e("LocationFragment", "onViewCreated: ", error)
                                }
                            )
                            {
                                override fun getHeaders(): MutableMap<String, String> {
                                    val headers = HashMap<String, String>()
                                    headers["Authorization"] = "Bearer $token";
                                    return headers
                                };
                            }
                            queue.add(locs)
                        } else {
                            root.findNavController().navigate(R.id.nav_login)
                        }
                    }
                    2 -> {
                        data1.visibility = View.GONE
                        data2.visibility = View.GONE
                        data3.visibility = View.VISIBLE
                        var al = root.findViewById<ListView>(R.id.lv_docs)
                        val spinner2 = root.findViewById<ProgressBar>(R.id.progressBar4);
                        val textView2: TextView = root.findViewById(R.id.tv_loadingdocs)

                        spinner2.visibility = View.VISIBLE
                        textView2.visibility = View.VISIBLE
                        al.visibility = View.GONE

                        var locs: JsonObjectRequest = object : JsonObjectRequest(
                            Request.Method.GET,
                            "https://api.pariscaretakerservices.fr/docuser",
                            null,
                            Response.Listener{ content ->
                                var listdoc: MutableList<Document> = mutableListOf<Document>()
                                try {
                                    val cur_jso =content.getJSONArray("data")
                                    for ( i in 0..cur_jso.length()-1){
                                        val loc = cur_jso.getJSONObject(i)
                                        val document = Document(
                                            loc.getLong("id"),
                                            loc.getString("nom"),
                                            loc.getString("path"),
                                        )
                                        listdoc.add(document)
                                    }
                                    val adapter = DocumentAdapter(requireContext(), listdoc)
                                    al.adapter = adapter

                                    spinner2.visibility = View.GONE
                                    textView2.visibility = View.GONE
                                    al.visibility = View.VISIBLE

                                } catch (e: Exception) {
                                    Log.e("DocumentFragment", "onViewCreated: ", e)
                                }
                            },
                            Response.ErrorListener { error ->
                                Log.e("DocumentFragment", "onViewCreated: ", error)
                            }
                        )
                        {
                            override fun getHeaders(): MutableMap<String, String> {
                                val headers = HashMap<String, String>()
                                headers["Authorization"] = "Bearer $token";
                                return headers
                            };
                        }
                        queue.add(locs)

                    }
                }
            }

            override fun onTabUnselected(tab: TabLayout.Tab?) {

            }

            override fun onTabReselected(tab: TabLayout.Tab?) {

            }
        })
        return root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {

        super.onViewCreated(view, savedInstanceState)

    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}