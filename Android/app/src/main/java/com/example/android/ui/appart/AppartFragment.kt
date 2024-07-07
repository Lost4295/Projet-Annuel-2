package com.example.android.ui.appart
import android.annotation.SuppressLint
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.ImageView
import android.widget.TextView
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.lifecycle.ViewModelProvider
import androidx.navigation.fragment.findNavController
import com.example.android.R
import com.example.android.databinding.FragmentAppartBinding
import com.example.android.databinding.FragmentGalleryBinding
import com.squareup.picasso.Picasso

class AppartFragment : Fragment() {

    private var _binding: FragmentAppartBinding? = null

    // This property is only valid between onCreateView and
    // onDestroyView.
    private val binding get() = _binding!!

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        val galleryViewModel =
            ViewModelProvider(this).get(AppartViewModel::class.java)

        _binding = FragmentAppartBinding.inflate(inflater, container, false)
        val root: View = binding.root
        return root
    }


    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        var data = arguments
        var appartImage = view.findViewById<ImageView>(R.id.appartImage)
        var appartTitle = view.findViewById<TextView>(R.id.appartTitle)
        var appartDescription = view.findViewById<TextView>(R.id.appartDescription)
        var appartPrice = view.findViewById<TextView>(R.id.appartPrice)
        var appartFullAddress = view.findViewById<TextView>(R.id.appartAddress)
        var appartNbchambers = view.findViewById<TextView>(R.id.appartNbchambers)
        var appartNbbathrooms = view.findViewById<TextView>(R.id.appartNbbathrooms)
        var appartNbBeds = view.findViewById<TextView>(R.id.appartNbBeds)
        var appartSurface = view.findViewById<TextView>(R.id.appartSurface)
        var btn = view.findViewById<Button>(R.id.btn_do_reserv)
        var shp = requireContext().getSharedPreferences("user", 0)
        var name = shp.getString("name", null)
        btn.visibility = View.GONE
        if (data != null) {
            Picasso.get().load("https://pariscaretakerservices.fr/images/appartements/"+data.getString("image")).into(appartImage)
            appartTitle.text = data.getString("titre")
            appartDescription.text = data.getString("shortDesc")
            appartPrice.text = data.getInt("price").toString() + " €/ nuit"
            appartFullAddress.text =
                "${data.getString("address")}, ${data.getString("city")}, ${data.getString("country")}"
            appartNbchambers.text = data.getInt("nbchambers").toString() + " chambres"
            appartNbbathrooms.text = data.getInt("nbbathrooms").toString() + " salles de bain"
            appartNbBeds.text = data.getInt("nbBeds").toString() + " lits"
            appartSurface.text = data.getInt("surface").toString() + " m²"
            if (name != null){
                btn.visibility = View.VISIBLE
                btn.setOnClickListener {
                    findNavController().navigate(R.id.nav_reservation, data)
                }
            }

        } else {
            Toast.makeText(context, "No data", Toast.LENGTH_LONG).show()
            findNavController().popBackStack()
        }
    }
    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}