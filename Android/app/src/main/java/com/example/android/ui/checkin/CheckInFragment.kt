package com.example.android.ui.checkin


import android.content.Context
import android.content.DialogInterface
import android.content.Intent
import android.graphics.Bitmap
import android.nfc.NfcAdapter
import android.os.Bundle
import android.provider.MediaStore
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.ImageView
import android.widget.LinearLayout
import android.widget.ProgressBar
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.fragment.app.Fragment
import androidx.navigation.fragment.findNavController
import com.android.volley.AuthFailureError
import com.android.volley.Request
import com.android.volley.Response
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import com.example.android.R
import com.example.android.databinding.FragmentCheckinBinding
import com.example.android.ui.appart.Appart
import com.example.android.ui.appart.AppartAdapter
import com.google.android.material.snackbar.Snackbar
import com.ncorti.slidetoact.SlideToActView
import org.json.JSONArray
import org.json.JSONException
import org.json.JSONObject


class CheckInFragment : Fragment() {

    private var _binding: FragmentCheckinBinding? = null

    // This property is only valid between onCreateView and
    // onDestroyView.
    private val binding get() = _binding!!
    private var nfcAdapter: NfcAdapter? = null
    private val pic_id = 123;
    // Define the button and imageview type variable
    lateinit var camera_open_id: Button
    lateinit var click_image_id: ImageView
    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {

        _binding = FragmentCheckinBinding.inflate(inflater, container, false)
        val root: View = binding.root

        nfcAdapter = NfcAdapter.getDefaultAdapter(requireContext())
        if (nfcAdapter == null) {
            Toast.makeText(requireContext(), "NFC is not available", Toast.LENGTH_LONG).show()
        }
        camera_open_id = root.findViewById(R.id.camera_button);
        click_image_id = root.findViewById(R.id.click_image);



        // Camera_open button is for open the camera and add the setOnClickListener in this button
        camera_open_id.setOnClickListener({
            // Create the camera_intent ACTION_IMAGE_CAPTURE it will open the camera for capture the image
            var camera_intent :Intent= Intent(MediaStore.ACTION_IMAGE_CAPTURE);
            // Start the activity with camera_intent, and request pic id
            startActivityForResult(camera_intent, pic_id);

        });

        return root
    }




    // This method will help to retrieve the image
    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data);
        // Match the request 'pic id with requestCode
        if (requestCode == pic_id) {
            // BitMap is data structure of image file which store the image in memory
            var photo: Bitmap= data?.getExtras()?.get("data") as Bitmap
            // Set the image in imageview for display
            click_image_id.setImageBitmap(photo);
            val sta = requireView().findViewById(R.id.example) as SlideToActView
            sta.visibility = View.VISIBLE
        }
    }


    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        val data = arguments
        val nfc = data?.getString("nfc")
        val load = view.findViewById<TextView>(R.id.tv_load)
        val prog = view.findViewById<ProgressBar>(R.id.progressBarc)
        val ll = view.findViewById<LinearLayout>(R.id.llcheck)
        load.visibility = View.VISIBLE
        prog.visibility = View.VISIBLE
        ll.visibility = View.GONE
        var queue = Volley.newRequestQueue(requireContext())
        var token: String? = requireContext().getSharedPreferences("user", Context.MODE_PRIVATE).getString("usrtoken", null)
        if (token == null) {
            Toast.makeText(requireContext(), "Merci de vous connecter.", Toast.LENGTH_LONG).show()
            findNavController().navigate(R.id.nav_login)
        } else {
        var json = JSONObject()
        try {
            json.put("id", nfc.toString().replace("pcs://", ""))
        } catch (e: JSONException) {
            e.printStackTrace()
        }
        var req : JsonObjectRequest = object : JsonObjectRequest(
            Request.Method.POST,
            "https://api.pariscaretakerservices.fr/checkin",
            json,
            Response.Listener{ content ->
                val data = content.getString("data")
                Log.e("data", data)
                if (data == "NOK") {
                    Toast.makeText(requireContext(), "Location non trouvée", Toast.LENGTH_LONG).show()
                    findNavController().navigate(R.id.nav_home)
                }

                load.visibility = View.GONE
                prog.visibility = View.GONE
                ll.visibility = View.VISIBLE

                var tvnomappart = view.findViewById<TextView>(R.id.tvnomappart)
                var finaltext = "Vous êtes sur le point de vous enregistrer dans l'appartement \"" + content.getString("title") + "\".\nVeuillez prendre une photo de la porte d'entrée pour valider votre enregistrement."
                tvnomappart.text = finaltext
                val sta = view.findViewById(R.id.example) as SlideToActView
                sta.visibility = View.GONE
                sta.onSlideCompleteListener = object : SlideToActView.OnSlideCompleteListener {
                    override fun onSlideComplete(view: SlideToActView) {
                        val builder: AlertDialog.Builder = AlertDialog.Builder(requireContext())
                            .setMessage("La porte est ouverte. Vous pouvez maintenant accéder à l'appartement ! N'hésitez pas à renseigner une note ! Passez un excellent séjour chez PCS !")
                            .setTitle("Tout est bon !")
                            .setCancelable(false)
                            .setPositiveButton("OK",
                                { dialog: DialogInterface, which: Int ->
                                    dialog.cancel()
                                })
                        val alertDialog: AlertDialog = builder.create()
                        alertDialog.show()
                        findNavController().navigate(R.id.nav_home)

                    }
                }
            },
            Response.ErrorListener { error ->
                if (error.networkResponse != null) {
                    val statusCode = error.networkResponse.statusCode
                    val errorMessage = String(error.networkResponse.data, charset("UTF-8"))
                    Log.e("hhn", errorMessage)
                    Toast.makeText(requireContext(), "Error: $statusCode - $errorMessage", Toast.LENGTH_LONG).show()
                } else {
                    //Toast.makeText(requireContext(), error.message, Toast.LENGTH_LONG).show()
                    Toast.makeText(requireContext(), error.toString(), Toast.LENGTH_LONG).show()
                    Log.e("herhdertfee", error.toString())

                }
            }
        ){
            @Throws(AuthFailureError::class)
            override fun getHeaders(): Map<String, String> {
                var params: MutableMap<String, String> = mutableMapOf<String,String>() ;
                params.put("Authorization","Bearer $token")
                params.put("Accept","*/*")
                params.put("Content-Type","application/json")

                //..add other headers
                return params
            }
        };
        queue.add(req)
    }}

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}